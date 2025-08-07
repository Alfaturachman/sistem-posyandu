# tinggi_badan.py
import serial
import re

ser = serial.Serial('COM4', 9600, timeout=1)
raw_data = ser.readline().decode().strip()

# Misalnya dapat: "#69.65A15.88*"
match = re.match(r"#(\d+\.\d+)A(\d+\.\d+)\*", raw_data)

if match:
    tinggi = match.group(1)
    berat = match.group(2)
    print(f"{tinggi},{berat}")  # Output seperti: 69.65,15.88
else:
    print("0,0")  # Jika parsing gagal
