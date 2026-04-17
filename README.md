# WorkFlowTrack
### Task and Progress Management System
**SE-II Group 9** | Bryan Fernandez | Abhiram Bhogi | Jin Carballosa | Gabriel Xirinachs

---

## Overview
WorkFlowTrack is a web-based Task and Progress Management System built with PHP and MySQL. It allows organizations to create, assign, and track tasks across teams with role-based access control, workflow lifecycle management, audit logging, and reporting.

---

## Features
- Role-based login (Admin, Project Manager, Team Member, Department Management)
- Task creation, assignment, and CRUD operations
- Workflow tracking (Pending to In Progress to Done)
- Task history and audit trail
- Notification center
- Report generation with PDF and CSV export

---

## Tech Stack
| Layer | Technology |
|-------|------------|
| Frontend | HTML5, CSS3, JavaScript |
| Backend | PHP 8.x |
| Database | MySQL |
| Auth | Session-based with bcrypt password hashing |
| Version Control | Git + GitHub |

---

## Project Structure
- config/ - Database connection
- includes/ - Shared PHP helpers
- public/ - All user-facing PHP screens
- assets/ - CSS and JavaScript
- database/ - SQL schema and seed data
- docs/ - Project documentation
- tests/ - Functional test cases

---

## Setup Instructions
1. Clone the repository: git clone https://github.com/gabrielxirinachs-source/WorkFlowTrack.git
2. Import the database schema: mysql -u root -p < database/workflowtrack.sql
3. Configure your database connection in config/db.php
4. Serve using XAMPP pointing to the /public folder
5. Navigate to http://localhost/WorkFlowTrack/public/index.php

---

## Branch Strategy
| Branch | Purpose |
|--------|---------|
| main | Stable production-ready code |
| dev | Integration branch - features merged here first |
| feature/auth | Login, logout, session management |
| feature/dashboard | Dashboard KPI screen |
| feature/task-management | Task CRUD operations |
| feature/workflow | Workflow status tracking |

---

## Team Roles
| Name | Role |
|------|------|
| Bryan Fernandez | Project Manager and Backend Developer |
| Abhiram Bhogi | Frontend Developer |
| Jin Carballosa | QA Engineer |
| Gabriel Xirinachs | System Admin and Documentation |

---

## Default Login Credentials (Demo)
| Username | Password | Role |
|----------|----------|------|
| admin | admin123 | System Administrator |
| manager | manager123 | Project Manager |
| member1 | member123 | Team Member |
| deptmgr | dept123 | Department Management |

---

## License
This project was developed for academic purposes as part of a Software Engineering course.
