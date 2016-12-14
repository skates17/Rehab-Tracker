//
//  ViewController.swift
//  Rehab Tracker
//
//  Created by Sean Kates on 11/1/16.
//  Copyright © 2016 CS 275 Project Group 6. All rights reserved.
//

import UIKit
import CoreData
import Foundation

class ViewController: UIViewController {
    
    //function to create a User and add in their userID
    func saveUserID(_ thisUserID: String) {
        //save entered userID to persistent core data
        let context = (UIApplication.shared.delegate as! AppDelegate).persistentContainer.viewContext
        let user = User(context: context)
        user.userID = thisUserID
        (UIApplication.shared.delegate as! AppDelegate).saveContext()
    }
    
    //function to delete ALL instances of User
    //used when someone wants to overwrite the current login with a new one
    func deleteUsers() -> Void
    {
        //get context working and create fetch request for all instances of User
        let appDelegate = UIApplication.shared.delegate as! AppDelegate
        let context = appDelegate.persistentContainer.viewContext
        let request = NSFetchRequest<NSFetchRequestResult>(entityName: "User")
        let deleteRequest = NSBatchDeleteRequest(fetchRequest: request)
        let persistentStoreCoordinator = context.persistentStoreCoordinator!
        
        do
        {
            try persistentStoreCoordinator.execute(deleteRequest, with: context)
            //saving the deletions
            try context.save()
        }
        //catch errors
        catch let error as NSError {
            debugPrint(error)
        }
    }
    
    //function to delete ALL instances of User
    //used when someone wants to overwrite the current login with a new one
    func deleteSession() -> Void
    {
        //get context working and create fetch request for all instances of User
        let appDelegate = UIApplication.shared.delegate as! AppDelegate
        let context = appDelegate.persistentContainer.viewContext
        let persistentStoreCoordinator = context.persistentStoreCoordinator!
        let sessionRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Session")
        let deleteSessionRequest = NSBatchDeleteRequest(fetchRequest: sessionRequest)
        
        do
        {
            try persistentStoreCoordinator.execute(deleteSessionRequest, with: context)
            //saving the deletions
            try context.save()
        }
            //catch errors
        catch let error as NSError {
            debugPrint(error)
        }
    }
    
    //function to display all instances
    func displayAllUsers()
    {
        let appDelegate = UIApplication.shared.delegate as! AppDelegate
        let context = appDelegate.persistentContainer.viewContext
        let request: NSFetchRequest<User>=User.fetchRequest()
        request.returnsObjectsAsFaults = false
        do{
            let results = try context.fetch(request)
            for item in results{
                for key in item.entity.attributesByName.keys{
                    let value: Any? = item.value(forKey: key)
                    print("\(key) = \(value)")
                }
            }
        }catch{
            print("Could not find stats. \(error)")
        }
    }
    
    //returns currently logged in userID as string
    func returnCurrentUsersID() -> String
    {
        let appDelegate = UIApplication.shared.delegate as! AppDelegate
        let context = appDelegate.persistentContainer.viewContext
        let request: NSFetchRequest<User>=User.fetchRequest()
        request.returnsObjectsAsFaults = false
        do
        {
            let results = try context.fetch(request)
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

    //responds to add user button and checks to see if theres already a user logged in
    //saves user to coredata and allows for overwrites
    @IBAction func AddUser(_ sender: AnyObject) {
        
        if self.numberOfUsers() > 0
        {
            //creates pop-up alert UIAlertController
            let overWriteAlert = UIAlertController(title: "Overwrite",
                                                   message: "There is already a User logged in, would you like to overwrite them? Warning: This will erase their data.",
                                                   preferredStyle: .alert)
            
            //creates the save button in the alert
            let saveAction = UIAlertAction(title: "Yes", style: .default, handler:
            {
                                            (action:UIAlertAction) -> Void in
                                            //delete other user
                                            while self.numberOfUsers() != 0
                                            {
                                                //delete sessions
                                                self.deleteUsers()
                                            }
                                            /*while self.numberOfSessions() != 0
                                            {
                                                //delete sessions
                                                self.deleteSession()
                                            }*/
                
                                            //alert to take input and save it
                                            //creates pop-up alert UIAlertController
                                            let alert = UIAlertController(title: "Username",
                                                                          message: "Add a new username",
                                                                          preferredStyle: .alert)
                                            
                                            //creates the save button in the alert
                                            let saveAction = UIAlertAction(title: "Save",
                                                                           style: .default,
                                                                           handler: { (action:UIAlertAction) -> Void in
                                                                            
                                                                            //calls saveUserID function with input as arguement
                                                                            let textField = alert.textFields!.first
                                                                            self.saveUserID(textField!.text!)
                                                                            self.viewDidLoad()
                                            })
                                            
                                            //creates the cancel button which exits without saving input
                                            let cancelAction = UIAlertAction(title: "Cancel",
                                                                             style: .default) { (action: UIAlertAction) -> Void in
                                            }
                                            
                                            //textfield for input
                                            alert.addTextField {
                                                (textField: UITextField) -> Void in
                                            }
                                            
                                            alert.addAction(saveAction)
                                            alert.addAction(cancelAction)
                                            self.present(alert,
                                                    animated: true,
                                                    completion: nil)
            })
        
        
            //creates the cancel button which exits without saving input
            let cancelAction = UIAlertAction(title: "No",
                                             style: .default) { (action: UIAlertAction) -> Void in }
            
            overWriteAlert.addAction(saveAction)
            overWriteAlert.addAction(cancelAction)
            self.present(overWriteAlert, animated: true, completion: nil)
        }
        if self.numberOfUsers() == 0
        {
            //alert to take input and save it
            let alert = UIAlertController(title: "Username",
                                          message: "Add a new username",
                                          preferredStyle: .alert)
            
            //creates the save button in the alert
            let saveAction = UIAlertAction(title: "Save",
                                           style: .default,
                                           handler: { (action:UIAlertAction) -> Void in
                                            
                                            //calls saveUserID function with input as arguement
                                            let textField = alert.textFields!.first
                                            self.saveUserID(textField!.text!)
                                            self.viewDidLoad()
            })
            
            //creates the cancel button which exits without saving input
            let cancelAction = UIAlertAction(title: "Cancel",
                                             style: .default) { (action: UIAlertAction) -> Void in }
            
            //textfield for input
            alert.addTextField {(textField: UITextField) -> Void in }
            alert.addAction(saveAction)
            alert.addAction(cancelAction)
            self.present(alert,
                         animated: true,
                         completion: nil)
        }
    }

    //returns number of users in Core Data
    func numberOfUsers() -> Int {
        let appDelegate = UIApplication.shared.delegate as! AppDelegate
        let context = appDelegate.persistentContainer.viewContext
        let request: NSFetchRequest<User>=User.fetchRequest()
        request.returnsObjectsAsFaults = false
        do
        {
            let results = try context.fetch(request)
            return results.count
        }
        catch let error as NSError
        {
            debugPrint(error)
            return -1
        }
    }
    
    //returns number of users in Core Data
    func numberOfSessions() -> Int {
        let appDelegate = UIApplication.shared.delegate as! AppDelegate
        let context = appDelegate.persistentContainer.viewContext
        let request: NSFetchRequest<Session>=Session.fetchRequest()
        request.returnsObjectsAsFaults = false
        do
        {
            let results = try context.fetch(request)
            return results.count
        }
        catch let error as NSError
        {
            debugPrint(error)
            return -1
        }
    }

    @IBAction func Continue(_ sender: UIButton) {
        //if username is valid, allows the user to continue into the app
        if self.getDatabaseUsername() == self.returnCurrentUsersID()
        {
            //continues on to the syncviewcontroller
            let storyBoard : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
            let nextViewController = storyBoard.instantiateViewController(withIdentifier: "Sync")
            self.present(nextViewController, animated:true, completion:nil)
        }
        else
        {
        //shows alert letting the user know they dont have a valid login and to add a new username
            //alert to tell user they arent properly logged in
            let alert = UIAlertController(title: "Invalid Login",
                                          message: "Sorry, you are logged in with an invalid username. Please enter a valid username by clicking the 'Add User' button on the top right of the screen.",
                                          preferredStyle: .alert)
            
            //creates the okay button in the alert
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
    
    //global variable to store returned userID check from database
    var returnedUserID = "DEFAULTVALUE"
    
    //function to check if a username is in the database, if yes, returns the name as string
    func getDatabaseUsername() -> String {
        
        //create urlstr string with current userID
        let urlstr : String = "http://www.uvm.edu/~bgoodwin/Restful/example.php?pmkPatientID=" + self.returnCurrentUsersID()
        
        //make url string into actual url and catch errors
        guard let url = URL(string: urlstr)
            else
            {
            print("Error: cannot create URL")
            return "Error creating URL!"
            }
        
        //creates urlRequest using our url
        //let urlRequest = NSMutableURLRequest(url: url)
        let urlRequest = URLRequest(url: url)
        let task = URLSession.shared.dataTask(with: urlRequest, completionHandler:
        {
            (data, response, error) in
            //if data exists, grab it and set it to our global variable
            if (error == nil)
            {
                //print(data as? String)
                let jo : NSDictionary
                do
                {
                    jo =
                        try JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                }
                catch
                {
                    return
                }
                if let name = jo["pmkPatientID"]
                {
                    self.returnedUserID = name as! String
                }
            }
        })
        //return value of returnedUserID
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

