import serial
import requests
import time

# Laravel API endpoint
LARAVEL_URL = "http://127.0.0.1:8000/api/weight"

# Serial port configuration
#ser = serial.Serial(
#    port="COM3",       # change to your actual COM port
#    baudrate=9600,
#    bytesize=serial.EIGHTBITS,
#    parity=serial.PARITY_NONE,
#    stopbits=serial.STOPBITS_ONE,
#    timeout=1
#)

print("Reading from Mettler Toledo IND570...")

while True:
    try:
        line = ser.readline().decode("utf-8").strip()
        if line:
            print(f"Weight Data: {line}")
            # Send to Laravel
            requests.post(LARAVEL_URL, json={"weight": line})
        time.sleep(2)
    except Exception as e:
        print("Error:", e)
        time.sleep(2)
