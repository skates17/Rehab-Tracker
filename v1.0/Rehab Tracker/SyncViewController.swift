//
//  SyncViewController.swift
//  Rehab Tracker
//
//  Created by Sean Kates on 11/2/16.
//  Copyright © 2016 CS 275 Project Group 6. All rights reserved.
//

import UIKit
import CoreData
import Foundation

class SyncViewController: UIViewController {
    
    //Array and dictionary to hold stats
    var sessions = [Session]()
    var stats:[(sessionID: String, avg_ch1_intensity:String, avg_ch2_intensity:String, date: String, session_compliance: String)]?
    
    //global variable comments to store comments per session
    var comments = "No Comments"
    
    @IBAction func showInfo(_ sender: UIBarButtonItem)
    {
        // create the alert
        let alert = UIAlertController(title: "Problems?", message: "Make sure your device is in range and paired with your phone!", preferredStyle: UIAlertControllerStyle.alert)
        
        // add an action (button)
        alert.addAction(UIAlertAction(title: "OK", style: UIAlertActionStyle.default, handler: nil))
        
        // show the alert
        self.present(alert, animated: true, completion: nil)
        
        //self.searchForStats()
    }
    
    @IBAction func Sync(_ sender: UIButton)
    {
        //alert to take input and save it
        let alert = UIAlertController(title: "Comments",
                                      message: "Please enter comments on your previous training sessions!",
                                      preferredStyle: .alert)
        
        //creates the save button in the alert
        let saveAction = UIAlertAction(title: "Continue",
                                       style: .default,
                                       handler: { (action:UIAlertAction) -> Void in
                                        let textField = alert.textFields!.first
                                        //saves input to global textfield
                                        self.comments = textField!.text!
                                        //call parseCSV to grab data
                                        self.parseCSV()
                                        //need to call function to push saved core data to database
                                        //self.pushToDatabase()
        })
        
        //textfield for input
        alert.addTextField {(textField: UITextField) -> Void in}
        alert.addAction(saveAction)
        self.present(alert, animated: true, completion: nil)
    }
    
    //returns currently logged in userID as string
    func getCurrentUsersID() -> String
    {
        let appDelegate = UIApplication.shared.delegate as! AppDelegate
        let contextU = appDelegate.persistentContainer.viewContext
        let request: NSFetchRequest<User>=User.fetchRequest()
        request.returnsObjectsAsFaults = false
        do
        {
            let results = try contextU.fetch(request)
            for item in results
            {
                for key in item.entity.attributesByName.keys
                {
                    let value: Any? = item.value(forKey: key)
                    return value as! String
                }
            }
        }
        catch
        {
            print("Could not find stats. \(error)")
            return "No userID"
        }
        return "Didnt Work"
    }

    
    //Function to parse data from csv
    func parseCSV()
    {
        // name of the database file, with newline-separated records
        guard let dbfile = Bundle.main.path(forResource: "practiceData", ofType: "csv")else{
            return
        }
        
        // record field delimeter (is a comma for csv)
        let delimeter = ","
        self.stats = []
        
        do
        {
            let db = try NSString(contentsOfFile:dbfile, encoding: String.Encoding.utf8.rawValue)
            let lines:[String] = db.components(separatedBy: "\n") as [String]
            
            for line in lines
            {
                var fields = [String]()
                if(line != "")
                {
                    fields = line.components(separatedBy: delimeter)
                    let stat = (sessionID: fields[0], avg_ch1_intensity:fields[1], avg_ch2_intensity:fields[2], date: fields[3], session_compliance: fields[4])
                    self.stats?.append(stat)
                }
            }
            self.addData()
        }
        catch let error as NSError
        {
            print("file \(dbfile) input failed \(error), \(error.userInfo)")
        }
    }
    
    //Function to add data from csv to core data
    func addData()
    {
        let context = (UIApplication.shared.delegate as! AppDelegate).persistentContainer.viewContext
        let sesEntity = NSEntityDescription.entity(forEntityName: "Session", in: context)
        
        
        for stat in stats!
        {
            let session = NSManagedObject(entity: sesEntity!, insertInto: context)as! Session
            session.sessionID = stat.sessionID
            session.session_compliance = stat.session_compliance
            session.avg_ch1_intensity = stat.avg_ch1_intensity
            session.avg_ch2_intensity = stat.avg_ch2_intensity
            session.date = stat.date
            session.notes = self.comments
            session.hasUser?.userID = self.getCurrentUsersID()
        }
        (UIApplication.shared.delegate as! AppDelegate).saveContext()
        self.searchForStats()
        
    }
    
    //Function to retrieve stats from core data
    func searchForStats()
    {
        
        let appDelegate = UIApplication.shared.delegate as! AppDelegate
        let context = appDelegate.persistentContainer.viewContext
        let request: NSFetchRequest<Session> = Session.fetchRequest()
        //let uRequest: NSFetchRequest<User> = User.fetchRequest()
        request.returnsObjectsAsFaults = false
        //uRequest.returnObjectsAsFaults = false
        
        do{
            sessions = try context.fetch(request)
            
            for val in sessions {
                //print all stats
                print(val)
                
                //assign values for post variables
                fldSessNum = val.sessionID!
                fldSessionCompliance = val.session_compliance
                fldIntensity1 = val.avg_ch1_intensity!
                fldIntensity2 = val.avg_ch2_intensity!
                fldDate = val.date!
                fldNote = self.comments
                pmkPatientID = self.getCurrentUsersID()
                self.thisDate()
                self.pushToDatabase()
                //print(pmkSessNum)
            }
            print(sessions.count)
            
            
            //print(sessions)
            
        }catch{
            print("Could not find stats. \(error)")
        }
        
    }
    
    func thisDate(){
        let currDate = Date()
        let formatter = DateFormatter()
        formatter.dateFormat = "yyyy/MM/dd"
        fldLastUpdate = formatter.string(from: currDate)
        
    }
    //Variables to hold stats for post function
    var pmkPatientID = ""
    var fldSessNum = ""
    var fldSessionCompliance = ""
    var fldIntensity1 = ""
    var fldIntensity2 = ""
    var fldDate = ""
    var fldNote = ""
    var fldLastUpdate = ""
    
    
    func pushToDatabase(){
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
        print(urlurl ?? String())
        //make url string into actual url and catch errors
        guard let url = URL(string: urlurl!)
            else
        {
            print("Error: cannot create URL")
            return
        }
        
        //creates urlRequest using our url
        
        var urlRequest = URLRequest(url: url)
        urlRequest.httpMethod = "POST"
        
    
        
        let task = URLSession.shared.dataTask(with: urlRequest, completionHandler:{
            (data, response, error) in
            if error != nil{
                print("error is \(error)")
                return;
            }
           
            
        })
        //return value of returnedUserID
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
    

    /*
    // MARK: - Navigation

    // In a storyboard-based application, you will often want to do a little preparation before navigation
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        // Get the new view controller using segue.destinationViewController.
        // Pass the selected object to the new view controller.
    }
    */

}
