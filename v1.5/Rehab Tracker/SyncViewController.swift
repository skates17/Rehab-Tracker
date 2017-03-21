//
//  SyncViewController.swift
//  Rehab Tracker
//
//  Created by Sean Kates on 11/2/16.
//  Copyright © 2016 CS 275 Project Group 6. All rights reserved.
//
// Need to give website props for the tab bar icon
// <a href="https://icons8.com">Icon pack by Icons8</a>
//

import UIKit
import CoreData
import Foundation
import CoreBluetooth

protocol BLEDelegate1 {
    func bleDidUpdateState()
    func bleDidConnectToPeripheral()
    func bleDidDisconenctFromPeripheral()
    func bleDidReceiveData(data: NSData?)
}

class SyncViewController: UIViewController, CBCentralManagerDelegate, CBPeripheralDelegate  {
    
    // Array and dictionary to hold stats
    private var sessions = [Session]()
    private var stats:[(sessionID: String, avg_ch1_intensity:String, avg_ch2_intensity:String, date: String, session_compliance: String)]?
    
    // global variable comments to store session comments
    private var comments = "No Comments"
    
    // Variables to hold stats for pushToDatabase function
    private var pmkPatientID = ""
    private var fldSessNum = ""
    private var fldSessionCompliance = ""
    private var fldIntensity1 = ""
    private var fldIntensity2 = ""
    private var fldDate = ""
    private var fldNote = ""
    private var fldLastUpdate = ""
    
    @IBAction func showInfo(_ sender: UIBarButtonItem) {
        // create the alert
        let alert = UIAlertController(title: "Problems?", message: "Make sure your device is in range and paired with your phone!", preferredStyle: UIAlertControllerStyle.alert)
        
        // add an action (button)
        alert.addAction(UIAlertAction(title: "OK", style: UIAlertActionStyle.default, handler: nil))
        
        // show the alert
        self.present(alert, animated: true, completion: nil)
    }
    
    // Show an alert if there was an error with syncing the data
    private func syncErrorAlert() {
        // create the alert
        let alert = UIAlertController(title: "Sync Error", message: "There was an error syncing your data, please try again!", preferredStyle: UIAlertControllerStyle.alert)
        
        // add an action (button)
        alert.addAction(UIAlertAction(title: "OK", style: UIAlertActionStyle.default, handler: nil))
        
        // show the alert
        self.present(alert, animated: true, completion: nil)
    }
    
    @IBAction func Sync(_ sender: UIButton) {
        // Alert to take input and save it
        let alert = UIAlertController(title: "Comments",
                                      message: "Please enter comments on your previous training sessions!",
                                      preferredStyle: .alert)
        
        // Creates the save button in the alert
        let saveAction = UIAlertAction(title: "Continue",
                                       style: .default,
                                       handler: { (action:UIAlertAction) -> Void in
                                        let textField = alert.textFields!.first
                                        
                                        // Saves input to global comments var
                                        self.comments = textField!.text!
                                        
                                        // Call parseCSV to grab data
                                        do {
                                            Util.overwriteSessions()
                                            try self.parseCSV()
                                        }
                                        catch {
                                            print("Could not find stats. \(error)")
                                            
                                            // Sync Error Alert
                                            self.syncErrorAlert()
                                        }
        })
        
        // textfield for input
        alert.addTextField {(textField: UITextField) -> Void in}
        alert.addAction(saveAction)
        self.present(alert, animated: true, completion: nil)
    }
    
    // Function to parse data from CSV file
    private func parseCSV() throws {
        // Name of the database file, with newline-separated records
        guard let dbfile = Bundle.main.path(forResource: "practiceData", ofType: "csv")else{
            return
        }
        
        // Record field delimeter (is a comma for csv)
        let delimeter = ","
        self.stats = []
        
        do {
            let db = try NSString(contentsOfFile:dbfile, encoding: String.Encoding.utf8.rawValue)
            let lines:[String] = db.components(separatedBy: "\n") as [String]
            
            for line in lines {
                var fields = [String]()
                if(line != "") {
                    fields = line.components(separatedBy: delimeter)
                    let stat = (sessionID: fields[0], avg_ch1_intensity:fields[1], avg_ch2_intensity:fields[2], date: fields[3], session_compliance: fields[4])
                    self.stats?.append(stat)
                }
            }
            self.addData()
        }
        catch let error as NSError {
            // Sync Error Alert
            self.syncErrorAlert()
            
            // Print error
            print("file \(dbfile) input failed \(error), \(error.userInfo)")
        }
    }
    
    // Function to add data from csv to core data
    private func addData() {
        let appDelegate = UIApplication.shared.delegate as! AppDelegate
        let context = appDelegate.persistentContainer.viewContext
        let sesEntity = NSEntityDescription.entity(forEntityName: "Session", in: context)
    
        for stat in stats! {
            let session = NSManagedObject(entity: sesEntity!, insertInto: context)as! Session
            session.sessionID = stat.sessionID
            session.session_compliance = stat.session_compliance
            session.avg_ch1_intensity = stat.avg_ch1_intensity
            session.avg_ch2_intensity = stat.avg_ch2_intensity
            session.date = stat.date
            session.notes = self.comments
            session.hasUser = Util.returnCurrentUser()
        }
        (UIApplication.shared.delegate as! AppDelegate).saveContext()
        self.searchForStats()
        
    }
    
    // Function to retrieve stats from core data
    private func searchForStats() {
        let appDelegate = UIApplication.shared.delegate as! AppDelegate
        let context = appDelegate.persistentContainer.viewContext
        let request: NSFetchRequest<Session> = Session.fetchRequest()
        request.returnsObjectsAsFaults = false
        
        do {
            sessions = try context.fetch(request)
            
            for val in sessions {
                
                // Assign values for post variables
                fldSessNum = val.sessionID!
                fldSessionCompliance = val.session_compliance
                fldIntensity1 = val.avg_ch1_intensity!
                fldIntensity2 = val.avg_ch2_intensity!
                fldDate = val.date!
                fldNote = self.comments
                pmkPatientID = Util.returnCurrentUsersID()
                self.thisDate()
                self.pushToDatabase()
            }
            
        }catch {
            // Sync Error Alert
            self.syncErrorAlert()
            
            print("Could not find stats. \(error)")
        }
        
    }
    
    // Gets the Data for Sync()
    private func thisDate() {
        let currDate = Date()
        let formatter = DateFormatter()
        formatter.dateFormat = "yyyy/MM/dd"
        fldLastUpdate = formatter.string(from: currDate)
    }
    
    private func pushToDatabase() {
        let urlstr : String = "https://www.uvm.edu/~bgoodwin/Restful/example.php?pmkPatientID="
            + pmkPatientID
            + "&fldSessNum="
            + fldSessNum
            + "&fldSessionCompliance="
            + fldSessionCompliance
            + "&fldIntensity1="
            + fldIntensity1
            + "&fldIntensity2="
            + fldIntensity2
            + "&fldDate="
            + fldDate
            + "&fldNote="
            + fldNote
            + "&fldLastUpdate="
            + fldLastUpdate
        
        let urlurl = urlstr.addingPercentEncoding(withAllowedCharacters: CharacterSet.urlQueryAllowed)
        
        //Make url string into actual url and catch errors
        guard let url = URL(string: urlurl!)
            else {
            // Sync Error Alert
            self.syncErrorAlert()
            
            print("Error: cannot create URL")
            return
        }
        
        // Creates urlRequest using our url
        var urlRequest = URLRequest(url: url)
        urlRequest.httpMethod = "POST"
        
        let task = URLSession.shared.dataTask(with: urlRequest, completionHandler:{
            (data, response, error) in
            if error != nil {
                // Sync Error Alert
                self.syncErrorAlert()
                
                print("error is \(error)")
                return;
            }
        })
        
        // Return value of returnedUserID
        task.resume()
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view.
        self.centralManager = CBCentralManager(delegate: self, queue: nil)
        self.data = NSMutableData()
    }
    
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    //***************************//
    // MARK: Bluetooth (BLE) Code
    
    /*
     CoreBluetooth Process:
     1. When view loads, centralManager is declared and checks the centralManager.state,
        If and only if the state = .poweredOn can the centralManager do anything.
        To check for the state, centralManagerDidUpdateState is called and updates state.
     2. To scan for BLE, startScanning() is called as long as state = .poweredOn
     3. The centralManager then calls scanForPeripherals(services) which scans for BLE devices with specific services.
        These services are determined by the UUID string on the BLE device.
     4. If a device is found with that service, then didDiscoverPeripheral() is called.
     5. didDiscoverPeripheral() then attempts to connect to the peripheral that was discovered, if the connect is
        successful, didConnect() is called. If its unsuccessful, didFailToConnect() is called.
    */
 
    // Initialize the UUID's
    let RBL_SERVICE_UUID = "713D0000-503E-4C75-BA94-3148F18D941E"
    let RBL_CHAR_TX_UUID = "713D0002-503E-4C75-BA94-3148F18D941E"
    let RBL_CHAR_RX_UUID = "713D0003-503E-4C75-BA94-3148F18D941E"
    
    //
    let my_UUID = "023DD007-7C99-447F-BE6A-9B9F18287FFB"
    
    // Initialize the BLEDelgate as delegate
    var delegate: BLEDelegate1?
    
    // Initialize all needed variables (make private)
    private      var centralManager:   CBCentralManager!
    private      var activePeripheral: CBPeripheral?
    private      var characteristics = [String : CBCharacteristic]()
    private      var data:             NSMutableData?
    private(set) var peripherals     = [CBPeripheral]()
    private      var RSSICompletionHandler: ((NSNumber?, NSError?) -> ())?
    
    // Private function to stop the scan after the scan has timed-out
    @objc private func scanTimeout() {
        self.centralManager.stopScan()
    }
    
    // Function that gets called when the connect button is clicked on the screen
    // Starts scanning for periferals
    @IBAction func connectBLE(_ sender: Any) {
        let scan = self.startScanning(timeout: 5)
        if scan == false {
            print("Scan Never Started")
        }
    }
    
    // MARK: Public methods
    
    // Function to start scanning for BLE's, takes the timeout as a boolean, returns boolean
    func startScanning(timeout: Double) -> Bool {
        
        if self.centralManager.state != .poweredOn {
            print("[ERROR] Couldn´t start scanning")
            return false
        }
        
        print("**************************")
        print("[DEBUG] Scanning started")
        
        // CBCentralManagerScanOptionAllowDuplicatesKey
        
        // Creates a timer to check the time
        Timer.scheduledTimer(timeInterval: timeout, target: self, selector: #selector(self.scanTimeout), userInfo: nil, repeats: false)
        
        // Initialize the services and call the scanForPeripherals functions with the services
        let services:[CBUUID] = [CBUUID(string: RBL_SERVICE_UUID)]
        self.centralManager.scanForPeripherals(withServices: services, options: nil)
        
        // return true if the scanning happened
        return true
    }
    
    // Method to connect to peripheral that we have found
    func connectToPeripheral(peripheral: CBPeripheral) -> Bool {
        
        if self.centralManager.state != .poweredOn {
            
            print("[ERROR] Couldn´t connect to peripheral")
            return false
        }
        
        print("[DEBUG] Connecting to peripheral: \(peripheral.identifier.uuidString)")
        
        self.centralManager.connect(peripheral, options: [CBConnectPeripheralOptionNotifyOnDisconnectionKey : NSNumber(value: true)])
        
        return true
    }
    
    // Method to disconnect from the peripheral
    func disconnectFromPeripheral(peripheral: CBPeripheral) -> Bool {
        
        if self.centralManager.state != .poweredOn {
            
            print("[ERROR] Couldn´t disconnect from peripheral")
            return false
        }
        
        self.centralManager.cancelPeripheralConnection(peripheral)
        
        return true
    }
    
    // MARK: CBCentralManager delegate
    func centralManagerDidUpdateState(_ central: CBCentralManager) {
        
        switch central.state {
        case .poweredOn:
            print("[DEBUG] Central manager state: Powered on")
            break
            
        case .unknown:
            print("[DEBUG] Central manager state: Unknown")
            break
            
        case .resetting:
            print("[DEBUG] Central manager state: Resseting")
            break
            
        case .unsupported:
            print("[DEBUG] Central manager state: Unsopported")
            break
            
        case .unauthorized:
            print("[DEBUG] Central manager state: Unauthorized")
            break
            
        case .poweredOff:
            print("[DEBUG] Central manager state: Powered off")
            break
        }
        
        self.delegate?.bleDidUpdateState()
    }
    
    // Function called if peripherals are discovered during scanForPeripherals() function call with specific services
    func centralManager(_ central: CBCentralManager, didDiscoverPeripheral peripheral: CBPeripheral, advertisementData: [String : AnyObject],RSSI: NSNumber) {
        print("[DEBUG] Found peripheral:", peripheral.identifier.uuidString, "With RSSI:", RSSI)
        
        if (!peripherals.contains(peripheral)){
            peripherals.append(peripheral)
        }
        
        // Try to connect to the peripheral
        let success = self.connectToPeripheral(peripheral: peripheral)
        if success == true {
            
        }
    }
    
    // If the connection was unsuccessful, print an error saying we failed to connect
    func centralManager(_ central: CBCentralManager, didFailToConnect peripheral: CBPeripheral, error: Error?) {
        print("[ERROR] Could not connecto to peripheral \(peripheral.identifier.uuidString) error: \(error)")
    }
    
    // If the connection was successful, set the activePeripheral to peripheral and discoverServices()
    func centralManager(central: CBCentralManager, didConnectPeripheral peripheral: CBPeripheral) {
        
        print("[DEBUG] Connected to peripheral \(peripheral.identifier.uuidString)")
        
        self.activePeripheral = peripheral
        
        self.activePeripheral?.delegate = self
        self.activePeripheral?.discoverServices([CBUUID(string: RBL_SERVICE_UUID)])
        
        self.delegate?.bleDidConnectToPeripheral()
    }
    
    func centralManager(_ central: CBCentralManager, didDisconnectPeripheral peripheral: CBPeripheral, error: Error?) {
        
        var text = "[DEBUG] Disconnected from peripheral: \(peripheral.identifier.uuidString)"
        
        if error != nil {
            text += ". Error: \(error)"
        }
        
        print(text)
        
        self.activePeripheral?.delegate = nil
        self.activePeripheral = nil
        self.characteristics.removeAll(keepingCapacity: false)
        
        self.delegate?.bleDidDisconenctFromPeripheral()
    }
    
    // MARK: CBPeripheral delegate
    func peripheral(_ peripheral: CBPeripheral, didDiscoverServices error: Error?) {
        
        if error != nil {
            print("[ERROR] Error discovering services. \(error)")
            return
        }
        
        print("[DEBUG] Found services for peripheral: \(peripheral.identifier.uuidString)")
        
        
        for service in peripheral.services! {
            let theCharacteristics = [CBUUID(string: RBL_CHAR_RX_UUID), CBUUID(string: RBL_CHAR_TX_UUID)]
            
            peripheral.discoverCharacteristics(theCharacteristics, for: service)
        }
    }
    
    func peripheral(_ peripheral: CBPeripheral, didDiscoverCharacteristicsFor service: CBService, error: Error?) {
        
        if error != nil {
            print("[ERROR] Error discovering characteristics. \(error)")
            return
        }
        
        print("[DEBUG] Found characteristics for peripheral: \(peripheral.identifier.uuidString)")
        
        for characteristic in service.characteristics! {
            self.characteristics[characteristic.uuid.uuidString] = characteristic
        }
        
        enableNotifications(enable: true)
    }
    
    func peripheral(_ peripheral: CBPeripheral, didUpdateValueFor characteristic: CBCharacteristic, error: Error?) {
        
        if error != nil {
            
            print("[ERROR] Error updating value. \(error)")
            return
        }
        
        if characteristic.uuid.uuidString == RBL_CHAR_TX_UUID {
            
            self.delegate?.bleDidReceiveData(data: characteristic.value as NSData?)
        }
    }
    
    func peripheral(_ peripheral: CBPeripheral, didReadRSSI RSSI: NSNumber, error: Error?) {
        self.RSSICompletionHandler?(RSSI, error as NSError?)
        self.RSSICompletionHandler = nil
    }
    
    func read() {
        
        guard let char = self.characteristics[RBL_CHAR_TX_UUID] else { return }
        
        self.activePeripheral?.readValue(for: char)
    }
    
    // Writes data
    func write(data: NSData) {
        
        guard let char = self.characteristics[RBL_CHAR_RX_UUID] else { return }
        
        self.activePeripheral?.writeValue(data as Data, for: char, type: .withoutResponse)
    }
    
    func enableNotifications(enable: Bool) {
        
        guard let char = self.characteristics[RBL_CHAR_TX_UUID] else { return }
        
        self.activePeripheral?.setNotifyValue(enable, for: char)
    }
    
    func readRSSI(completion: @escaping (_ RSSI: NSNumber?, _ error: NSError?) -> ()) {
        
        self.RSSICompletionHandler = completion
        self.activePeripheral?.readRSSI()
    }
}
