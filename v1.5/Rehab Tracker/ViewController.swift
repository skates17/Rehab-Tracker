//
//  ViewController.swift
//  Rehab Tracker
//
//  Created by Sean Kates on 11/1/16.
//  Copyright © 2017 UVM Medical Center. All rights reserved.
//

import UIKit
import CoreData
import Foundation
import CoreBluetooth

class ViewController: UIViewController {

    // Function to create a User and add in their userID
    private func saveUserID(_ thisUserID: String) {
        // Save entered userID to persistent core data
        let context = (UIApplication.shared.delegate as! AppDelegate).persistentContainer.viewContext
        let user = User(context: context)
        user.userID = thisUserID
        (UIApplication.shared.delegate as! AppDelegate).saveContext()
    }

    // Responds to add user button and checks to see if theres already a user logged in
    // Saves user to coredata and allows for overwrites
    @IBAction func AddUser(_ sender: AnyObject) {
        
        if Util.numberOfUsers() > 0 {
            // Creates pop-up alert UIAlertController
            let overWriteAlert = UIAlertController(title: "Overwrite",
                                                   message: "There is already a User logged in, would you like to overwrite them? Warning: This will erase their data.",
                                                   preferredStyle: .alert)
            
            // Creates the save button in the alert
            let saveAction = UIAlertAction(title: "Yes", style: .default, handler:
            {
                                            (action:UIAlertAction) -> Void in
                                            // Delete other user
                                            while Util.numberOfUsers() != 0 {
                                                // Delete all core data
                                                Util.deleteData()
                                            }
                
                                            // Alert to take input and save it
                                            // Creates pop-up alert UIAlertController
                                            let alert = UIAlertController(title: "Username",
                                                                          message: "Add a new username",
                                                                          preferredStyle: .alert)
                                            
                                            // Creates the save button in the alert
                                            let saveAction = UIAlertAction(title: "Save",
                                                                           style: .default,
                                                                           handler: { (action:UIAlertAction) -> Void in
                                                                            
                                                                            // Calls saveUserID function with input as arguement
                                                                            let textField = alert.textFields!.first
                                                                            self.saveUserID(textField!.text!)
                                                                            self.viewDidLoad()
                                            })
                                            
                                            // Creates the cancel button which exits without saving input
                                            let cancelAction = UIAlertAction(title: "Cancel",
                                                                             style: .default) { (action: UIAlertAction) -> Void in
                                            }
                                            
                                            // Textfield for input
                                            alert.addTextField {
                                                (textField: UITextField) -> Void in
                                            }
                                            
                                            alert.addAction(saveAction)
                                            alert.addAction(cancelAction)
                                            self.present(alert,
                                                    animated: true,
                                                    completion: nil)
            })
        
        
            // Creates the cancel button which exits without saving input
            let cancelAction = UIAlertAction(title: "No",
                                             style: .default) { (action: UIAlertAction) -> Void in }
            
            overWriteAlert.addAction(saveAction)
            overWriteAlert.addAction(cancelAction)
            self.present(overWriteAlert, animated: true, completion: nil)
        }
        if Util.numberOfUsers() == 0 {
            // Alert to take input and save it
            let alert = UIAlertController(title: "Username",
                                          message: "Add a new username",
                                          preferredStyle: .alert)
            
            // Creates the save button in the alert
            let saveAction = UIAlertAction(title: "Save",
                                           style: .default,
                                           handler: { (action:UIAlertAction) -> Void in
                                            
                                            //calls saveUserID function with input as arguement
                                            let textField = alert.textFields!.first
                                            self.saveUserID(textField!.text!)
                                            self.viewDidLoad()
            })
            
            // Creates the cancel button which exits without saving input
            let cancelAction = UIAlertAction(title: "Cancel",
                                             style: .default) { (action: UIAlertAction) -> Void in }
            
            // Textfield for input
            alert.addTextField {(textField: UITextField) -> Void in }
            alert.addAction(saveAction)
            alert.addAction(cancelAction)
            self.present(alert,
                         animated: true,
                         completion: nil)
        }
    }

    @IBAction func Continue(_ sender: UIButton) {
        // If username is valid, allows the user to continue into the app
        if self.getDatabaseUsername() == Util.returnCurrentUsersID() {
            // Continues on to the syncviewcontroller
            let storyBoard : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
            let nextViewController = storyBoard.instantiateViewController(withIdentifier: "Sync")
            self.present(nextViewController, animated:true, completion:nil)
        }
        else {
            // Alert to tell user they arent properly logged in
            let alert = UIAlertController(title: "Invalid Login",
                                          message: "Sorry, you are logged in with an invalid username. Please enter a valid username by clicking the 'Add User' button on the top right of the screen.",
                                          preferredStyle: .alert)
            
            // Creates the okay button in the alert
            let okayAction = UIAlertAction(title: "Okay",
                                           style: .default,
                                           handler: { (action:UIAlertAction) -> Void in
            })
            alert.addAction(okayAction)
            self.present(alert,
                         animated: true,
                         completion: nil)
        }
    }
    
    // Global variable to store returned userID check from database
    private var returnedUserID = "DEFAULTVALUE"
    
    // Function to check if a username is in the database, if yes, returns the name as string
    private func getDatabaseUsername() -> String {
        
        // Create urlstr string with current userID
        let urlstr : String = "https://www.uvm.edu/~bgoodwin/Restful/example.php?pmkPatientID=" + Util.returnCurrentUsersID()
        
        // Make url string into actual url and catch errors
        guard let url = URL(string: urlstr)
            else {
            print("Error: cannot create URL")
            return "Error creating URL!"
            }
        
        // Creates urlRequest using our url
        // Let urlRequest = NSMutableURLRequest(url: url)
        let urlRequest = URLRequest(url: url)
        let task = URLSession.shared.dataTask(with: urlRequest, completionHandler: {
            (data, response, error) in
            // If data exists, grab it and set it to our global variable
            if (error == nil) {
                let jo : NSDictionary
                do {
                    jo =
                        try JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                }
                catch {
                    return
                }
                if let name = jo["pmkPatientID"] {
                    self.returnedUserID = name as! String
                }
            }
        })
        // Return value of returnedUserID
        task.resume()
        return self.returnedUserID
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        _ = self.getDatabaseUsername()
        
        // Do any additional setup after loading the view, typically from a nib.
    }


    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }


}

