# Smart Parking System 🚗

A web-based Smart Parking System that automates parking space tracking, number plate scanning, fee calculation, and user notifications. Built using PHP, MySQL, HTML, CSS, JavaScript, and Python.

## 🌟 Features

- 🔐 **User Registration & Login**  
  Secure account creation with a unique `user_xxxxx` ID.

- 🧾 **Car Details Management**  
  Users can enter and manage their car information.

- 🅿️ **Parking Session Tracking**  
  - Automatic in/out time recording (**via number plate scanning**).
  - Initial parking session status set to **"unpaid"**.
  - Parking fee calculated **per hour**.

- 🔍 **Number Plate Scanning**  
  - Uses a Python OCR script to detect and extract vehicle numbers from images.
  - Extracted numbers are matched with registered users to log entry/exit.
  - Supports webcam or image upload-based detection.

- 📲 **Billing and Notifications**  
  - Bills generated and sent to the user’s mobile number.
  - Easy status tracking (paid/unpaid).

- 🗃️ **Database Integration**  
  - Data stored securely in a MySQL database.
  - All backend operations handled via PHP.

## 🛠️ Tech Stack

| Tech         | Use                               |
|--------------|------------------------------------|
| PHP          | Server-side scripting              |
| MySQL        | Database                           |
| HTML/CSS     | Frontend structure and style       |
| JavaScript   | Interactivity and validation       |
| Python       | Number plate scanning (OCR module) |
| Tesseract    | OCR engine for text recognition    |
| OpenCV       | Image preprocessing                |
| XAMPP        | Localhost development              |

## 📁 Folder Structure

