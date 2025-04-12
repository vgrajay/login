@echo off
:: Start XAMPP (Apache and MySQL)
start "" "C:\xampp\xampp_start.exe"

:: Wait for services to start (you can increase if needed)
timeout /t 8 /nobreak >nul

:: Navigate to your project directory
cd /d "C:\xampp\htdocs\Login and Signup Form Design"

:: Run your Python app
python app.py

pause
