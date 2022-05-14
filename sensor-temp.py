#!/usr/bin/env python
import time # Importer la bibliothèque time
import board # Importer la bibliothèque board
import busio # Importer la bibliothèque busio
import adafruit_scd30 # Importer la bibliothèque adafruit_scd30

i2c = busio.I2C(board.SCL, board.SDA, frequency=10000) # Créer un objet i2c à 10kHz
scd = adafruit_scd30.SCD30(i2c) # Créer un objet scd

while True: # Boucle infinie
    if scd.data_available: # Si des données sont disponibles
        print("%0.2f" % scd.temperature) # Afficher les données de température
        quit() # Quitter le programme
    time.sleep(0.5) # Attendre 0.5s