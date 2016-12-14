//
//  LaunchScreenViewController.swift
//  Rehab Tracker
//
//  Created by Sean Kates on 12/8/16.
//  Copyright © 2016 CS 275 Project Group 6. All rights reserved.
//

import UIKit

class LaunchScreenViewController: UIViewController {

    override func viewDidLoad() {
        super.viewDidLoad()
        
        let jumpingJacks = UIImage.gifImageWithName("jumpingjacks")
        let imageView = UIImageView(image: jumpingJacks)
        imageView.frame = CGRect(x: 20.0, y: 180.0, width: self.view.frame.size.width - 40, height: 150.0)
        view.addSubview(imageView)
        // Do any additional setup after loading the view.
        let when = DispatchTime.now() + 2
        DispatchQueue.main.asyncAfter(deadline: when)
        {
            //move to nav
            self.performSegue(withIdentifier: "Delay", sender: nil)
        }
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
