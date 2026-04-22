To implement the comprehensive features required for the DepEd CAR Attendance Monitoring System while strictly maintaining the integrity of your existing **Attendance and Access Control MySQL Schema**, you can run the following script.

This script creates an **extension layer** using the `_ext` postfix for all new tables and properties to support organizational hierarchies, specific work arrangements, and the detailed attendance rules found in the sources.

```sql
-- ============================================
-- 1. ORGANIZATIONAL HIERARCHY EXTENSIONS
-- Supporting: Office -> Unit -> Position
-- ============================================

-- Categories for "Job Order", "Contract of Service", and "Plantilla"
CREATE TABLE EmploymentType_ext (
    TypeID INT PRIMARY KEY AUTO_INCREMENT,
    TypeName VARCHAR(50) NOT NULL 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Defines Units or Sections belonging to a Dept
CREATE TABLE Units_ext (
    UnitID INT PRIMARY KEY AUTO_INCREMENT,
    Deptid INT NOT NULL, -- FK to existing Dept table
    UnitName VARCHAR(100) NOT NULL,
    FOREIGN KEY (Deptid) REFERENCES Dept(Deptid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Defines Positions belonging to a Unit
CREATE TABLE Positions_ext (
    PositionID INT PRIMARY KEY AUTO_INCREMENT,
    UnitID INT NOT NULL, -- FK to Units_ext
    PositionTitle VARCHAR(100) NOT NULL,
    FOREIGN KEY (UnitID) REFERENCES Units_ext(UnitID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Extension for Userinfo to link to hierarchy and handle "Disable" feature
CREATE TABLE Userinfo_ext (
    Userid VARCHAR(20) PRIMARY KEY, -- FK to original Userinfo table
    TypeID INT,
    UnitID INT,
    PositionID INT,
    IsActive TINYINT(1) DEFAULT 1, -- Supports the "Disable Employee" UI button
    FOREIGN KEY (Userid) REFERENCES Userinfo(Userid),
    FOREIGN KEY (TypeID) REFERENCES EmploymentType_ext(TypeID),
    FOREIGN KEY (UnitID) REFERENCES Units_ext(UnitID),
    FOREIGN KEY (PositionID) REFERENCES Positions_ext(PositionID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 2. WORK ARRANGEMENT MANAGEMENT
-- Supporting: Full Flexi, Fixed Flexi, WFH
-- ============================================

CREATE TABLE WorkArrangements_ext (
    ArrangementID INT PRIMARY KEY AUTO_INCREMENT,
    Userid VARCHAR(20) NOT NULL,
    ArrangementType ENUM('Full Flexi', 'Fixed Flexi', 'WFH') NOT NULL,
    Schid INT, -- FK to original Schedule table
    CoveredPeriodStart DATE, -- Captures the "Covered Period"
    CoveredPeriodEnd DATE,
    PreferredWFHDays VARCHAR(100), -- Stores "Tue, Wed, Thu" etc.
    Status ENUM('Pending', 'Approved', 'Denied') DEFAULT 'Pending', -- For Dashboard tracking
    RecommendedBy VARCHAR(100), -- For official signature lines
    ApprovedBy VARCHAR(100),
    FOREIGN KEY (Userid) REFERENCES Userinfo(Userid),
    FOREIGN KEY (Schid) REFERENCES Schedule(Schid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 3. ATTENDANCE RULES & MONITORING
-- Supporting: Grace periods, Monday Flag Ceremony, Sync Status
-- ============================================

-- Metadata for rule constants like Monday 8:00 AM cutoff
CREATE TABLE AttendanceRules_ext (
    RuleKey VARCHAR(50) PRIMARY KEY,
    RuleValue VARCHAR(50),
    Description VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tracking biometric sync status for each log
CREATE TABLE Checkinout_ext (
    Logid INT PRIMARY KEY, -- FK to original Checkinout table
    IsSynced TINYINT(1) DEFAULT 0, -- Supports "Real-time biometric sync status"
    FOREIGN KEY (Logid) REFERENCES Checkinout(Logid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- 4. CALENDAR & DASHBOARD EXTENSIONS
-- Supporting: Holiday categories and Notifications
-- ============================================

-- Categorizes holidays as Local, National, or Suspension
CREATE TABLE Holiday_ext (
    Holidayid INT PRIMARY KEY, -- FK to original Holiday table
    HolidayType ENUM('Local', 'National', 'Suspension') NOT NULL,
    IsWorkSuspended TINYINT(1) DEFAULT 0,
    FOREIGN KEY (Holidayid) REFERENCES Holiday(Holidayid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Supports "Notifications & Alerts" (shift changes, tardiness warnings)
CREATE TABLE Notifications_ext (
    NotificationID INT PRIMARY KEY AUTO_INCREMENT,
    Userid VARCHAR(20) NOT NULL,
    Category VARCHAR(50), -- e.g., 'Tardiness warning', 'Shift change'
    Message TEXT,
    CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    IsRead TINYINT(1) DEFAULT 0,
    FOREIGN KEY (Userid) REFERENCES Userinfo(Userid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- SEED DATA FOR ATTENDANCE RULES
-- ============================================

INSERT INTO AttendanceRules_ext (RuleKey, RuleValue, Description) VALUES 
('MONDAY_CUTOFF', '08:00:00', 'Mandatory Monday time-in for Flag Ceremony'),
('GRACE_PERIOD_MINS', '15', 'Default grace period before considered LATE'),
('MIN_WORK_HOURS', '8', 'Minimum hours required to avoid UNDERTIME');
```

### **How this connects to your existing schema:**
*   **Hierarchy:** Instead of using the flat `Dept` table, the system uses `Units_ext` and `Positions_ext` to group employees as shown in the **Offices Page**.
*   **User Management:** The `Userinfo_ext` table links to your original `Userinfo` to store the new metadata (Employment Type, Position) without altering the biometric source table.
*   **Attendance Enforcement:** The `WorkArrangements_ext` table and `AttendanceRules_ext` metadata allow your Python backend to calculate **LATE** or **UNDERTIME** status by comparing raw `Checkinout` logs against the rules for **Monday Flag Ceremonies** or **Full Flexi** windows.
*   **Dashboard Sync:** The `Checkinout_ext` table specifically addresses the requirement for monitoring "biometric sync status" in real-time.