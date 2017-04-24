//
//  Session+CoreDataProperties.swift
//  Rehab Tracker
//
//  Created by Sean Kates on 12/5/16.
//  Copyright © 2017 UVM Medical Center. All rights reserved.
//

import Foundation
import CoreData

extension Session {

    @nonobjc public class func fetchRequest() -> NSFetchRequest<Session> {
        return NSFetchRequest<Session>(entityName: "Session");
    }

    @NSManaged public var avg_ch1_intensity: String?
    @NSManaged public var avg_ch2_intensity: String?
    @NSManaged public var notes: String?
    @NSManaged public var session_compliance: String
    @NSManaged public var sessionID: String?
    @NSManaged public var hasUser: User?

}
