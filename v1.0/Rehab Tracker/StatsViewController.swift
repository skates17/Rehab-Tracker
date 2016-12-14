//
//  StatsViewController.swift
//  Rehab Tracker
//
//  Created by Sean Kates on 12/8/16.
//  Copyright © 2016 CS 275 Project Group 6. All rights reserved.
//

import UIKit
import Charts
import Foundation
import CoreData

class StatsViewController: UIViewController {
    
    @IBOutlet weak var pieChartView: PieChartView!
    @IBOutlet weak var barChartView: BarChartView!
    
    //variables to hold data arrays
    var sessions: [Double]!
    var intensity: [Double]!
    var compliance: [Double]!
    
    //variable to hold array of sessions
    var coreSessions = [Session]()
    
    //variables to hold individual session stats
    var fldSessNum = ""
    var fldSessionCompliance = ""
    var fldIntensity1 = ""
    var fldIntensity2 = ""
    
    //set chart function to create a chart from session data
    func setBarChart(dataPoints: [Double], values: [Double]) {
        barChartView.noDataText = "You need to provide data for the chart."
        var dataEntries: [BarChartDataEntry] = []
        
        for i in 0..<dataPoints.count
        {
            let dataEntry = BarChartDataEntry(x: dataPoints[i], y: values[i])
            dataEntries.append(dataEntry)
        }
        
        let chartDataSet = BarChartDataSet(values: dataEntries, label: "Session Intensity")
        let chartData = BarChartData(dataSet: chartDataSet)
        barChartView.data = chartData
        
        //Aesthetic options for the chart
        barChartView.chartDescription?.text = ""
        barChartView.xAxis.labelPosition = .bottom
        barChartView.backgroundColor = UIColor(red: 189/255, green: 195/255, blue: 199/255, alpha: 1)
        barChartView.animate(xAxisDuration: 2.0, yAxisDuration: 2.0)
        barChartView.rightAxis.enabled = false
        
        //Setting a target line for duration
        let targetIntensity = ChartLimitLine(limit: 15.0, label: "")
        barChartView.rightAxis.addLimitLine(targetIntensity)
        
        //fix X axis values
        
    }
    
    func setPieChart(values: [Double]) {
        pieChartView.noDataText = "You need to provide data for the chart."
        var dataEntries: [ChartDataEntry] = []
        
        for i in 0..<values.count {
            let dataEntry = ChartDataEntry(x: Double(i), y: values[i])
            dataEntries.append(dataEntry)
        }
        
        let pieChartDataSet = PieChartDataSet(values: dataEntries, label: "Compliance")
        let pieChartData = PieChartData(dataSet: pieChartDataSet)
        pieChartView.data = pieChartData
        
        //Set color scheme to red and green
        var colors: [UIColor] = []
        let colorGreen = UIColor.green
        colors.append(colorGreen)
        let colorRed = UIColor.red
        colors.append(colorRed)
        pieChartDataSet.colors = colors
        
        //Add description text
        pieChartView.chartDescription?.text = "Session Compliance"
    }
    
    //Function to retrieve stats from core data
    func getStats()
    {
        let appDelegate = UIApplication.shared.delegate as! AppDelegate
        let context = appDelegate.persistentContainer.viewContext
        let request: NSFetchRequest<Session>=Session.fetchRequest()
        request.returnsObjectsAsFaults = false
        
        do
        {
            coreSessions = try context.fetch(request)
            
            for val in coreSessions
            {
                //assign values for post variables
                if(val.sessionID != nil){
                    fldSessNum = val.sessionID!
                    let fldSessNumDouble: Double = Double(fldSessNum)!
                    sessions.append(fldSessNumDouble)
                    print(fldSessNumDouble)
                }
                
//                let fldSessNumDouble = Double(fldSessNum)
//                sessions.append(fldSessNumDouble!)
//                
//                fldSessionCompliance = val.session_compliance
//                let fldSessComplianceDouble = Double(fldSessionCompliance)
//                compliance.append(fldSessComplianceDouble!)
//                
//                fldIntensity1 = val.avg_ch1_intensity!
//                fldIntensity2 = val.avg_ch2_intensity!
//                let intesityDouble1 = Double(fldIntensity1)
//                let intesityDouble2 = Double(fldIntensity2)
//                let realIntensity = intesityDouble1! + intesityDouble2!
//                intensity.append(realIntensity)
//                print("this is the realIntensity ")
            }
        }
        catch
        {
            print("Could not find stats. \(error)")
        }
    }
    
    func calculateCompliance() -> [Double]!
    {
        //counters for in and out of compliance
        var inCompliance = 0.0
        var outCompliance = 0.0
        
        //determine number of sessions in and out of compliance
        for i in 0..<compliance.count
        {
            if compliance[i] > 0
            {
                inCompliance = inCompliance + 1
            }
            
            if compliance[i] == 0
            {
                outCompliance = outCompliance + 1
            }
        }
        let calculatedCompliance = [inCompliance, outCompliance]
        return calculatedCompliance
    }
    
    override func viewDidLoad() {
        
        super.viewDidLoad()
        
        //get data for graphs
        //getStats()
        compliance = [1, 0, 1, 0, 1]
        sessions = [1,2,3,4,5]
        intensity = [8, 10, 16, 12, 19]
        
        let complianceArray = calculateCompliance()
        
        //send data to charts
        setBarChart(dataPoints: sessions, values: intensity)
        setPieChart(values: complianceArray!)
        
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
