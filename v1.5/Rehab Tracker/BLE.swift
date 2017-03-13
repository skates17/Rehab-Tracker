/*
Copyright (c) 2015 Fernando Reynoso

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

/*
 BLE Flow Chart
 - When an instance of the class is created, variables are initialized for the:
    -UUIDS
    -CBCentralManager
    -CBPeripheral
    -BLEDelegate
 - Then the CentralM
 */
import Foundation
import CoreBluetooth

protocol BLEDelegate {
    func bleDidUpdateState()
    func bleDidConnectToPeripheral()
    func bleDidDisconenctFromPeripheral()
    func bleDidReceiveData(data: NSData?)
}

class BLE: NSObject, CBCentralManagerDelegate, CBPeripheralDelegate {
    
    // Initialize the UUID's
    let RBL_SERVICE_UUID = "713D0000-503E-4C75-BA94-3148F18D941E"
    let RBL_CHAR_TX_UUID = "713D0002-503E-4C75-BA94-3148F18D941E"
    let RBL_CHAR_RX_UUID = "713D0003-503E-4C75-BA94-3148F18D941E"
    
    // Initialize the BLEDelgate as delegate
    var delegate: BLEDelegate?
    
    // Initialize all needed variables (make private)
    private      var centralManager:   CBCentralManager!
    private      var activePeripheral: CBPeripheral?
    private      var characteristics = [String : CBCharacteristic]()
    private      var data:             NSMutableData?
    private(set) var peripherals     = [CBPeripheral]()
    private      var RSSICompletionHandler: ((NSNumber?, NSError?) -> ())?
    
    // Override the initialize and call the CBCentralManager as self
    override init() {
        super.init()
        self.centralManager = CBCentralManager(delegate: self, queue: nil)
        self.data = NSMutableData()
    }
    
    // Private function to stop the scan after the scan has timed-out
    @objc private func scanTimeout() {
        
        print("[DEBUG] Scanning stopped")
        print("**************************")
        self.centralManager.stopScan()
    }
    
    // MARK: Public methods
    
    func startBLEProcess() {
        //self.centralManagerDidUpdateState(centralManager)
        self.delegate?.bleDidUpdateState()
        //self.startScanning(timeout: 15)
    }
    
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
        Timer.scheduledTimer(timeInterval: timeout, target: self, selector: #selector(BLE.scanTimeout), userInfo: nil, repeats: false)
        
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
        
        //print("[DEBUG] Connecting to peripheral: \(peripheral.identifier.UUIDString)")
        
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
    
    // MARK: CBCentralManager delegate
    func centralManagerDidUpdateState(_ central: CBCentralManager) {
        
        switch central.state {
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
            
        case .poweredOn:
            print("[DEBUG] Central manager state: Powered on")
            break
        }
        
        self.delegate?.bleDidUpdateState()
    }
    
    private func centralManager(central: CBCentralManager, didDiscoverPeripheral peripheral: CBPeripheral, advertisementData: [String : AnyObject],RSSI: NSNumber) {
        print("[DEBUG] Find peripheral: \(peripheral.identifier.uuidString) RSSI: \(RSSI)")
        
        //let index = peripherals.indexOf { $0.identifier.UUIDString == peripheral.identifier.uuidString }
        
        //if let index = index {
            //peripherals[index] = peripheral
        //} else {
        peripherals.append(peripheral)
        //}
    }
    
    func centralManager(_ central: CBCentralManager, didFailToConnect peripheral: CBPeripheral, error: Error?) {
        print("[ERROR] Could not connecto to peripheral \(peripheral.identifier.uuidString) error: \(error)")
    }
    
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
}
