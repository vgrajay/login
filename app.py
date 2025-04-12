from flask import Flask, request, jsonify, send_from_directory
from flask_cors import CORS
import smtplib, random, os, threading
from email.mime.text import MIMEText
from dotenv import load_dotenv
import mysql.connector

# Load environment variables
load_dotenv()

# Flask app setup
app = Flask(__name__, static_url_path='', static_folder='.')
CORS(app)

# Connect to MySQL
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="user_auth"
)
cursor = db.cursor()

# OTP store in memory
otp_store = {}

# Function to delete OTP from memory
def delete_otp(email):
    if email in otp_store:
        del otp_store[email]

# Function to delete OTP from MySQL table
def delete_otp_from_db(email):
    try:
        delete_query = "DELETE FROM otp_table WHERE email = %s"
        cursor.execute(delete_query, (email,))
        db.commit()
        print(f"OTP for {email} deleted from DB.")
    except Exception as e:
        print(f"Error deleting OTP for {email}: {e}")

# Route to serve frontend
@app.route('/')
def serve_index():
    return send_from_directory('.', 'index.html')

# Send OTP route
@app.route('/send-otp', methods=['POST'])
def send_otp():
    data = request.get_json()
    email = data.get('email')

    if not email:
        return jsonify({"status": "fail", "message": "Email is required"}), 400

    otp = random.randint(1000, 9999)
    otp_store[email] = str(otp)

    # Schedule OTP deletion after 5 minutes (300 sec)
    threading.Timer(60, delete_otp, args=[email]).start()
    threading.Timer(60, delete_otp_from_db, args=[email]).start()

    # Insert OTP into MySQL
    insert_query = "INSERT INTO otp_table (email, otp) VALUES (%s, %s)"
    try:
        cursor.execute(insert_query, (email, otp))
        db.commit()
    except Exception as e:
        return jsonify({"status": "fail", "message": f"DB error: {e}"}), 500

    # Email credentials from .env
    sender = os.getenv('EMAIL_USER')
    password = os.getenv('EMAIL_PASS')

    # Email message setup
    msg = MIMEText(f"Your OTP is: {otp}\nThis OTP will expire in 5 minutes.")
    msg['Subject'] = "Your OTP Code"
    msg['From'] = sender
    msg['To'] = email

    try:
        server = smtplib.SMTP('smtp.gmail.com', 587)
        server.starttls()
        server.login(sender, password)
        server.send_message(msg)
        server.quit()
        return jsonify({"status": "success", "message": "OTP sent successfully"})
    except Exception as e:
        return jsonify({"status": "fail", "message": f"Email error: {e}"}), 500

# Verify OTP route
@app.route('/verify-otp', methods=['POST'])
def verify_otp():
    data = request.get_json()
    email = data.get('email')
    user_otp = str(data.get('otp'))

    if otp_store.get(email) == user_otp:
        # Clear OTP after successful verification
        delete_otp(email)
        delete_otp_from_db(email)
        return jsonify({"status": "success", "message": "OTP Verified"})
    else:
        return jsonify({"status": "fail", "message": "Invalid or Expired OTP"}), 401

# Start server
if __name__ == "__main__":
    app.run(debug=True)
