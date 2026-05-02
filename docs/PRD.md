# PRODUCT REQUIREMENTS DOCUMENT (PRD)

## Product Name
**PAWrtner – Veterinary Clinic Management System**

---

## 1. Product Overview
PAWrtner is a web-based system designed to modernize veterinary clinic operations by replacing manual and fragmented processes with a centralized digital platform. It integrates administrative tools, electronic medical records (EMR), and a client portal to improve efficiency, accuracy, and communication.

The system targets small- to medium-sized veterinary clinics transitioning from paper-based workflows.

---

## 2. Problem Statement
Veterinary clinics face several issues:
- Manual record-keeping leads to inefficiency and data loss  
- Poor communication between veterinarians and pet owners  
- Lack of centralized systems for inventory, scheduling, and reports  
- Delayed services due to disorganized workflows  

PAWrtner solves these through a unified, accessible, and secure platform.

---

## 3. Goals and Objectives

### Primary Goal
Develop a web-based system that streamlines clinic operations and enhances client engagement.

### Specific Objectives
- Build an Administrative Module for staff, inventory, and reports  
- Implement Electronic Medical Records (EMR) for pets  
- Create a Client Portal for pet owners  
- Ensure secure access using Role-Based Access Control (RBAC)  
- Evaluate system quality using ISO/IEC 25010 standards  

---

## 4. Target Users

### Admin (Clinic Owner/Staff)
- Manage employees and roles  
- Track inventory and supplies  
- Generate financial reports  

### Veterinarian
- Create and update pet medical records  
- Access patient history and lab results  

### Pet Owner (Client)
- View pet records  
- Book appointments  
- Receive notifications and reminders  

---

## 5. Core Features

### 5.1 Administrative Module
- Staff management (roles, permissions)  
- Inventory tracking with low-stock alerts  
- Financial reporting dashboard  

### 5.2 Medical Records Module (EMR)
- Digital pet profiles  
- Vaccination tracking  
- Lab result uploads (PDF/image)  
- Real-time record updates  

### 5.3 Client Portal
- Pet health record access  
- Appointment scheduling (calendar view)  
- Automated reminders (visits, vaccines)  

### 5.4 Authentication & Security
- Role-Based Access Control (Admin, Vet, Client)  
- Secure login system  
- Data privacy compliance  

### 5.5 Notification System
- Appointment reminders  
- Vaccination alerts  
- Follow-up notifications  

---

## 6. Functional Requirements
- Users must log in before accessing features  
- System must route users based on roles  
- Data must be stored in a centralized database  
- Admin can manage all system data  
- Vet can access only medical-related data  
- Clients can only access their own pet records  
- System must support multiple users simultaneously  

---

## 7. Non-Functional Requirements

### Usability
- Simple and intuitive UI  
- Mobile-responsive design  

### Reliability
- Supports concurrent users without failure  

### Security
- Encrypted authentication  
- Role-based access restrictions  

### Performance
- Fast data retrieval and updates  

### Compliance
- Must align with ISO/IEC 25010 standards  

---

## 8. Scope

### Included
- Web-based system accessible via browser  
- Admin, Vet, and Client modules  
- Digital records and scheduling  
- Inventory and reporting  

### Not Included (Limitations)
- No online payment integration  
- No offline mode (internet required)  
- No AI-based diagnosis  
- No direct integration with medical equipment  

---

## 9. System Architecture

### Frontend
- HTML  
- CSS  
- JavaScript  
- Tailwind CSS  

### Backend
- PHP  
- Laravel  

### Database
- MySQL  

### Environment
- XAMPP (Apache + MySQL)  
- VS Code  

---

## 10. User Flow

1. User logs in  
2. System verifies role (RBAC)  
3. Redirect to dashboard:
   - Admin → Admin Panel  
   - Vet → Medical Records  
   - Client → Client Portal  
4. User performs actions (view, update, save)  
5. Data is stored in the database  

---

## 11. Success Metrics
- Reduced time in record retrieval  
- Increased appointment compliance  
- Improved user satisfaction  
- High system acceptance rating  

---

## 12. Development Methodology
**Model:** Rapid Application Development (RAD)

### Phases
1. Requirements Planning  
2. User Design  
3. Construction  
4. Deployment  

---

## 13. Future Enhancements
- Online payment integration (GCash, cards)  
- AI-assisted diagnostics  
- Mobile app version  
- Integration with medical devices  
- Multi-branch clinic support  

---

## 14. Key Value Proposition
PAWrtner connects clinic operations and client communication in one platform, improving efficiency, transparency, and overall pet care.