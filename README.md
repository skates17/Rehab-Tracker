# Rehab-Tracker
The overarching goal of the project was to create a mobile application/wireless-enabled NMES device to improve rehabilitation compliance of patients with ACL tears through increased monitoring and novel patient-provider interactions. The iOS app connects to the NMES electrotherapy device through bluetooth, and recieves data on the rehabilitation session. This data is saved in a database and is viewable to the physician on the physician-portal website. Copyright © University of Vermont.

Folder Directory:

-Rehab Tracker:
  -Primary Author: Sean Kates sean.kates@uvm.edu
  -The code for the mobile application(iOs) where patients upload their session information which writes to the database

-Arduino:
  -Primary Author: Sean Kates, SEED Team.
  -The code for the Blend arduino that will be uploaded to the board on the NMES machine.
  
-Restful: 
  -Primary Author: Brandon Goodwin brandon.goodwin@uvm.edu
  -All of the scripts that involve RESTful communication, PUSH notifications, and compliance scripts as well as database architecture

-PhysicianPortal:
  -Primary Author: Meaghan Winter meaghan.winter@uvm.edu
  -The code for the online portal which clinicians can use to add/manage users as well as track compliance and patient sessions
