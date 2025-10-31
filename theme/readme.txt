# Smart Waste Management System (PHP & MySQLi)

A complete web-based waste management platform designed to streamline the collection, sale, and purchase of waste materials. This system connects individuals or organizations who want their waste collected with registered waste drivers, and it also supports buyers who want to purchase recyclable waste. The platform includes an administrator panel for oversight, financial management, and operational control.

This project aims to promote environmental sustainability by digitizing waste management processes, reducing manual operations, and creating opportunities for waste-based businesses.

## Key Features

### Client (Waste Generator)
- User account registration and authentication  
- Dashboard to view available waste collection vehicles  
- Waste request booking based on location and waste type  
- Real-time calculation of collection fees based on waste weight (kg)  
- Online payment processing for waste pickup  
- Request tracking from booking to pickup completion  
- Ability to mark a collection as completed once the waste is picked  

### Driver (Waste Collector)
- Driver login and profile management  
- Dashboard to view assigned collection requests  
- Ability to accept and process waste pickup requests  
- Earnings tracking & payout updates based on completed jobs  
- Status update functionality (in-progress / completed)  
- Mark job as completed when waste is successfully collected  

### Waste Buyer Module
- Registration and login for individuals or businesses who want to purchase waste  
- Browse available waste types and quantities  
- Option to contact admin for purchase processing  
- Streamlined marketplace experience for recyclable waste trade  

### Administrator Panel
- Manage all users (clients, drivers, and buyers)  
- Verify and approve payments for waste pickups  
- Assign waste collection tasks to drivers  
- Approve driver earnings and issue payouts  
- Monitor waste bookings, status updates, and system activity  
- Full transaction and payment history  
- Manage waste categories, pricing, and system settings  

## Workflow Overview

1. User creates an account and submits a waste pickup request  
2. System calculates cost based on waste weight in kilograms  
3. User makes payment online  
4. Admin verifies payment and assigns a driver  
5. Driver completes pickup and updates status  
6. System records job completion and issues driver's earnings portion  
7. Both client and driver confirm job completion in the system  

## Technologies Used

- PHP (Core Application Logic)  
- MySQLi (Database Layer)  
- HTML, CSS, JavaScript (Frontend UI)  
- AJAX for smooth request handling  
- Session-based login authentication  
- Database design optimized for multi-role access  

## Project Objectives

- Digitize waste management and reduce manual handling  
- Promote a cleaner environment by encouraging proper waste disposal  
- Provide business opportunities for drivers and waste buyers  
- Ensure transparency in waste handling and payment processing  
- Build a scalable platform that can be expanded with mobile app support  

## Future Enhancements

- GPS location tracking and driver dispatch automation  
- Mobile application integration (Android & iOS)  
- Momo / more payment gateway support  
- Live waste pricing and analytics dashboard  
- AI waste classification (long-term upgrade)  

## Summary

This system bridges the gap between waste producers, collectors, and buyers through a digital platform designed with efficiency, transparency, and environmental responsibility in mind. It introduces automation into waste collection and trading workflows, empowering users and helping build a sustainable ecosystem around waste management.

Whether for community cleanup, business recycling solutions, or supporting eco-driven initiatives, this software serves as a foundation for scalable, modern waste management infrastructure.
