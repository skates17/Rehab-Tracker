
//
//  AppDelegate.swift
//  Rehab Tracker
//
//  Created by Sean Kates on 11/1/16.
//  Copyright © 2017 University of Vermont. All rights reserved.
//

import UIKit
import CoreData
import UserNotifications
import PusherSwift

@UIApplicationMain
class AppDelegate: UIResponder, UIApplicationDelegate {
    
    // Initialize the pusher instance with key from server
    //let pusher = Pusher(key: "1a12b25128b9b2b28ee1")
    
    var window: UIWindow?

    func application(_ application: UIApplication, didFinishLaunchingWithOptions launchOptions: [UIApplicationLaunchOptionsKey: Any]?) -> Bool {
        
        /* Push Notifications
        let center = UNUserNotificationCenter.current()
        center.requestAuthorization(options: [.alert, .sound]) { (granted, error) in
            // actions based on whether notifications were authorized or not
        }
        application.registerForRemoteNotifications()*/
        UNUserNotificationCenter.current().requestAuthorization(options: [.alert,.sound,.badge]){
            (granted,error) in
            if granted{
                application.registerForRemoteNotifications()
            } else {
                print("User Notification permission denied: \(String(describing: error?.localizedDescription))")
            }
        }
            
        
        // Define all the color schemes here for consistency
        // RED = 28, GREEN = 117, BLUE = 127
        // HEX = 1C757F
        // Navigation Bar Color Scheme
        UINavigationBar.appearance().barTintColor = UIColor.white
        UINavigationBar.appearance().tintColor = UIColor(red: 0.1098, green: 0.4588, blue: 0.498, alpha: 1.0)
        UINavigationBar.appearance().titleTextAttributes = [NSForegroundColorAttributeName:UIColor(red: 0.1098, green: 0.4588, blue: 0.498, alpha: 1.0)]
        
        // Tab Bar Color Scheme
        UITabBar.appearance().barTintColor = UIColor.white
        UITabBar.appearance().tintColor = UIColor(red: 0.1098, green: 0.4588, blue: 0.498, alpha: 1.0)
        
        // Button Color Scheme
        UIButton.appearance().setTitleColor(UIColor(red: 0.1098, green: 0.4588, blue: 0.498, alpha: 1.0), for: .normal)

        // Splash Screen Animation
        self.window = UIWindow(frame: UIScreen.main.bounds)
        self.window!.backgroundColor = UIColor(red: 0.1098, green: 0.4588, blue: 0.498, alpha: 1.0)
        self.window!.makeKeyAndVisible()
        
        // rootViewController from StoryBoard
        let storyboard : UIStoryboard = UIStoryboard(name: "Main", bundle: nil)
        let navigationController = storyboard.instantiateViewController( withIdentifier: "homeNavigation" )
        self.window!.rootViewController = navigationController
        
        // logo mask
        navigationController.view.layer.mask = CALayer()
        navigationController.view.layer.mask?.contents = UIImage(named: "Splash-Logo.png")!.cgImage
        navigationController.view.layer.mask?.bounds = CGRect(x: 0, y: 0, width: 60, height: 60)
        navigationController.view.layer.mask?.anchorPoint = CGPoint(x: 0.5, y: 0.5)
        navigationController.view.layer.mask?.position = CGPoint(x: navigationController.view.frame.width / 2, y: navigationController.view.frame.height / 2)
        
        // logo mask background view
        let maskBgView = UIView(frame: navigationController.view.frame)
        maskBgView.backgroundColor = UIColor.white
        navigationController.view.addSubview(maskBgView)
        navigationController.view.bringSubview(toFront: maskBgView)
        
        // logo mask animation
        let transformAnimation = CAKeyframeAnimation(keyPath: "bounds")
        transformAnimation.duration = 1
        transformAnimation.beginTime = CACurrentMediaTime() + 1 //add delay of 1 second
        let initalBounds = NSValue(cgRect: (navigationController.view.layer.mask?.bounds)!)
        let secondBounds = NSValue(cgRect: CGRect(x: 0, y: 0, width: 50, height: 50))
        let finalBounds = NSValue(cgRect: CGRect(x: 0, y: 0, width: 2000, height: 2000))
        transformAnimation.values = [initalBounds, secondBounds, finalBounds]
        transformAnimation.keyTimes = [0, 0.5, 1]
        transformAnimation.timingFunctions = [CAMediaTimingFunction(name: kCAMediaTimingFunctionEaseInEaseOut), CAMediaTimingFunction(name: kCAMediaTimingFunctionEaseOut)]
        transformAnimation.isRemovedOnCompletion = false
        transformAnimation.fillMode = kCAFillModeForwards
        navigationController.view.layer.mask?.add(transformAnimation, forKey: "maskAnimation")
        
        // logo mask background view animation
        UIView.animate(withDuration: 0.1,
                       delay: 1.5,
                       options: UIViewAnimationOptions.curveEaseIn,
                       animations: {
                        maskBgView.alpha = 0.0
        },
                       completion: { finished in
                        maskBgView.removeFromSuperview()
                        self.animationDidStop( finished: true )
        })
        
        // root view animation
        UIView.animate(withDuration: 0.25,
                       delay: 1.3,
                       options: [],
                       animations: {
                        self.window!.rootViewController!.view.transform = CGAffineTransform(scaleX: 1.05, y: 1.05)
        },
                       completion: { finished in
                        UIView.animate(withDuration: 0.3,
                                       delay: 0.0,
                                       options: UIViewAnimationOptions.curveEaseInOut,
                                       animations: {
                                        self.window!.rootViewController!.view.transform = CGAffineTransform.identity
                        },
                                       completion: nil
                        )
        })
        return true
    }
    

    func animationDidStop( finished flag: Bool ) {
        // remove mask when animation completes
        self.window!.rootViewController!.view.layer.mask = nil
    }
    
    // Push Notifications
    func application(_ application: UIApplication, didRegisterForRemoteNotificationsWithDeviceToken deviceToken: Data) {
        //print("Successful registration. Token is:")
        //print(tokenString(deviceToken))
    }
    
    func application(_ application: UIApplication, didFailToRegisterForRemoteNotificationsWithError error: Error) {
        print("Failed to register for remote notifications: \(error.localizedDescription)")
    }
    
    func tokenString(_ deviceToken:Data) -> String{
        //code to make a token string
        let bytes = [UInt8](deviceToken)
        var token = ""
        for byte in bytes{
            token += String(format: "%02x",byte)
        }
        return token
    }
    
    /*func application(_ application: UIApplication, didReceiveRemoteNotification notification: [AnyHashable : Any], fetchCompletionHandler completionHandler: @escaping (UIBackgroundFetchResult) -> Void) {
        print(notification)
    }*/

    func applicationWillResignActive(_ application: UIApplication) {
        // Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
        // Use this method to pause ongoing tasks, disable timers, and invalidate graphics rendering callbacks. Games should use this method to pause the game.
        self.saveContext()
    }

    func applicationDidEnterBackground(_ application: UIApplication) {
        // Use this method to release shared resources, save user data, invalidate timers, and store enough application state information to restore your application to its current state in case it is terminated later.
        // If your application supports background execution, this method is called instead of applicationWillTerminate: when the user quits.
        self.saveContext()
    }

    func applicationWillEnterForeground(_ application: UIApplication) {
        // Called as part of the transition from the background to the active state; here you can undo many of the changes made on entering the background.
    }

    func applicationDidBecomeActive(_ application: UIApplication) {
        // Restart any tasks that were paused (or not yet started) while the application was inactive. If the application was previously in the background, optionally refresh the user interface.
    }

    func applicationWillTerminate(_ application: UIApplication) {
        // Called when the application is about to terminate. Save data if appropriate. See also applicationDidEnterBackground:.
        // Saves changes in the application's managed object context before the application terminates.
        self.saveContext()
    }

    // MARK: - Core Data stack
    lazy var persistentContainer: NSPersistentContainer = {
        /*
         The persistent container for the application. This implementation
         creates and returns a container, having loaded the store for the
         application to it. This property is optional since there are legitimate
         error conditions that could cause the creation of the store to fail.
         */
        let container = NSPersistentContainer(name: "Rehab_Tracker")
        container.loadPersistentStores(completionHandler: { (storeDescription, error) in
            if let error = error as NSError? {
                fatalError("Unresolved error \(error), \(error.userInfo)")
            }
        })
        return container
    }()

    // MARK: - Core Data Saving support
    func saveContext () {
        let context = persistentContainer.viewContext
        if context.hasChanges {
            do {
                try context.save()
            } catch {
                // Replace this implementation with code to handle the error appropriately.
                // fatalError() causes the application to generate a crash log and terminate. You should not use this function in a shipping application, although it may be useful during development.
                let nserror = error as NSError
                fatalError("Unresolved error \(nserror), \(nserror.userInfo)")
            }
        }
    }
}

