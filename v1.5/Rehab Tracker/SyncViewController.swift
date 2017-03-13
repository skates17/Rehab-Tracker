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

class SyncViewController: UIViewController {
    
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
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
}
