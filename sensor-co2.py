#!/usr/bin/env python
import time
import board
import busio
import adafruit_scd30

i2c = busio.I2C(board.SCL, board.SDA, frequency=10000)
scd = adafruit_scd30.SCD30(i2c)

while True:
    if scd.data_available:
        print("%d" % scd.CO2)
        quit()
    time.sleep(0.5)