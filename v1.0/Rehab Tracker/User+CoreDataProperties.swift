//
//  User+CoreDataProperties.swift
//  Rehab Tracker
//
//  Created by Sean Kates on 12/5/16.
//  Copyright © 2016 CS 275 Project Group 6. All rights reserved.
//

import Foundation
import CoreData

extension User {

    @nonobjc public class func fetchRequest() -> NSFetchRequest<User> {
        return NSFetchRequest<User>(entityName: "User");
    }

    @NSManaged public var userID: String?
    @NSManaged public var hasSession: NSSet?

}

// MARK: Generated accessors for hasSession
extension User {

    @objc(addHasSessionObject:)
    @NSManaged public func addToHasSession(_ value: Session)

    @objc(removeHasSessionObject:)
    @NSManaged public func removeFromHasSession(_ value: Session)

    @objc(addHasSession:)
    @NSManaged public func addToHasSession(_ values: NSSet)

    @objc(removeHasSession:)
    @NSManaged public func removeFromHasSession(_ values: NSSet)

}
