//
//  Util.swift
//  Rehab Tracker
//
//  Created by Sean Kates on 2/14/17.
//  Copyright © 2017 CS 275 Project Group 6. All rights reserved.
//

import UIKit
import CoreData
import Foundation

class Util {
    
    // Returns current User's userID as a string
    class func returnCurrentUsersID() -> String {
        let appDelegate = UIApplication.shared.delegate as! AppDelegate
        let context = appDelegate.persistentContainer.viewContext
        let request: NSFetchRequest<User>=User.fetchRequest()
        request.returnsObjectsAsFaults = false
        do {
            let results = try context.fetch(request)
            for item in results {
                return item.userID!
            }
        }
        catch {
            print("Could not find stats. \(error)")
            return "No userID"
        }
        return "Didnt Work"
    }
    
    // Returns the current User as a NSManagedObject
    class func returnCurrentUser() -> User {
        let appDelegate = UIApplication.shared.delegate as! AppDelegate
        let context = appDelegate.persistentContainer.viewContext
        let request: NSFetchRequest<User>=User.fetchRequest()
        request.returnsObjectsAsFaults = false
        do {
            let results = try context.fetch(request)
            for user in results {
                return user
            }
        }
        catch {
            print("Could not find User. \(error)")
        }
        let userEntity = NSEntityDescription.entity(forEntityName: "User", in: context)
        let nothing = NSManagedObject(entity: userEntity!, insertInto: context)as! User
        return nothing as User
    }

    // Function to delete all Users and Sessions from Core Data
    class func deleteData() -> Void {
        // Get context
        let appDelegate = UIApplication.shared.delegate as! AppDelegate
        let context = appDelegate.persistentContainer.viewContext
        
        // Create the fetch requests
        let userRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "User")
        let sessionRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Session")
        
        // Create the delete requests
        let userDeleteRequest = NSBatchDeleteRequest(fetchRequest: userRequest)
        let sessionDeleteRequest = NSBatchDeleteRequest(fetchRequest: sessionRequest)
        let persistentStoreCoordinator = context.persistentStoreCoordinator!
        
        do {
            // Delete the sessions and then the Users
            try persistentStoreCoordinator.execute(sessionDeleteRequest, with: context)
            try persistentStoreCoordinator.execute(userDeleteRequest, with: context)
            
            // Save the changes
            try context.save()
        }
            // Catch Errors
        catch let error as NSError {
            debugPrint(error)
        }
    }
    
    // Function to delete all information from Core Data
    class func overwriteSessions() -> Void {
        // Get context
        let appDelegate = UIApplication.shared.delegate as! AppDelegate
        let context = appDelegate.persistentContainer.viewContext
        
        // Create the fetch request
        let sessionRequest = NSFetchRequest<NSFetchRequestResult>(entityName: "Session")
        
        // Create the delete request
        let sessionDeleteRequest = NSBatchDeleteRequest(fetchRequest: sessionRequest)
        let persistentStoreCoordinator = context.persistentStoreCoordinator!
        
        do {
            // Delete the sessions
            try persistentStoreCoordinator.execute(sessionDeleteRequest, with: context)
            
            // Save the changes
            try context.save()
        }
            // Catch Errors
        catch let error as NSError {
            debugPrint(error)
        }
    }
    
    // Returns number of users in Core Data
    class func numberOfUsers() -> Int {
        let appDelegate = UIApplication.shared.delegate as! AppDelegate
        let context = appDelegate.persistentContainer.viewContext
        let request: NSFetchRequest<User>=User.fetchRequest()
        request.returnsObjectsAsFaults = false
        do {
            let results = try context.fetch(request)
            return results.count
        }
        catch let error as NSError {
            debugPrint(error)
            return -1
        }
    }
    
    // Returns number of Sessions in Core Data
    class func numberOfSessions() -> Int {
        let appDelegate = UIApplication.shared.delegate as! AppDelegate
        let context = appDelegate.persistentContainer.viewContext
        let request: NSFetchRequest<Session>=Session.fetchRequest()
        request.returnsObjectsAsFaults = false
        do {
            let results = try context.fetch(request)
            return results.count
        }
        catch let error as NSError {
            debugPrint(error)
            return -1
        }
    }
    
    // Function to get the target intensity for specific user from the Database
    class func updateTargetIntensity() {
        
        // Create urlstr string with current userID
        let urlstr : String = "https://www.uvm.edu/~bgoodwin/Restful/getTargetIntensity.php?pmkPatientID="
            + Util.returnCurrentUsersID()
        
        // Make url string into actual url and catch errors
        guard let url = URL(string: urlstr)
            else {
                print("Error: cannot create URL")
                return
        }
        
        // Creates urlRequest using our url
        let urlRequest = URLRequest(url: url)
        let task = URLSession.shared.dataTask(with: urlRequest, completionHandler: {
            (data, response, error) in
            // If data exists, grab it and set it to our global variable
            if (error == nil) {
                let jo : NSDictionary
                do {
                    jo = try JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                }
                catch {
                    return
                }
                if let targetIntensity = jo["fldGoal"] {
                    let returnedTargetIntensity = (targetIntensity as! NSString)
                    
                    // Send the target intensity to the saveTargetIntensity function to save to Core Data
                    saveTargetIntensity(returnedTargetIntensity: returnedTargetIntensity as String)
                }
            }
        })
        // Resume the URL Request task
        task.resume()
    }
    
    // Function to save target intensity to Core Data
    class func saveTargetIntensity(returnedTargetIntensity: String) {
        let appDelegate = UIApplication.shared.delegate as! AppDelegate
        let context = appDelegate.persistentContainer.viewContext
        let request: NSFetchRequest< User >=User.fetchRequest()
        request.returnsObjectsAsFaults = false
        
        do {
            // Make the fetch request
            let coreUser = try context.fetch( request  )
            
            for user in coreUser {
                user.targetIntensity = returnedTargetIntensity
            }
        } catch {
            print("Could not find users. \(error)")
        }
        
        (UIApplication.shared.delegate as! AppDelegate).saveContext()
    }
    
    // Function to return the targetIntensity from core data
    class func returnTargetIntensity() -> Double {
        let appDelegate = UIApplication.shared.delegate as! AppDelegate
        let context = appDelegate.persistentContainer.viewContext
        let request: NSFetchRequest<User>=User.fetchRequest()
        request.returnsObjectsAsFaults = false
        do {
            let results = try context.fetch(request)
            for item in results {
                let stringTargetIntensity = item.targetIntensity!
                let targetIntensity: Double = Double( stringTargetIntensity )!
                return targetIntensity
            }
        }
        catch {
            print("Could not find stats. \(error)")
            return 0.0
        }
        return 0.0
    }
}
