# Rehab-Tracker iOS App

This is the mobile application codebase, created by Sean Kates. The main features are the BLE connection with the NMES device, and using Core Data to save/display data.

# Core Data Model

The data is saved from the NMES device into Core Data, below is a ER diagram of the Core Data model.

![ER Diagram](/Rehab Tracker/Assets.xcassets/Images/ER diagram.png?raw=true)

# Bluetooth Low Energy (BLE)

The BLE protocol is used to connect with the NMES device and retrieve the rehabiliation session data. The code in the Arduino folder writes the data using unsigned char array to the iOS app once it has established a stable connection. Once data has been recieved, we disconnect from the device, and sync the data to the database.
