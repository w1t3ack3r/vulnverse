# 💸 NaijaPay Staging Portal CTF: The Revenge of Bayo

## 📢 Challenge Overview

Welcome to the **NaijaPay Staging Portal**, a hands-on cybersecurity challenge designed to test your full-stack exploitation skills. This environment simulates a rapidly scaled FinTech application where the lead developer, "Bayo," has left five critical, chained vulnerabilities across the application and system layers.

Your mission is to audit the system, starting from zero, and escalate to container **ROOT** while also exploiting financial logic flaws to steal the hidden flag.

### 🎯 Objective Summary

1.  **Gain Initial Access:**
2.  **Steal Flags:**
3.  **Financial Domination:**

---

## 🛠️ Setup & Prerequisites

This CTF is designed to be run locally using Docker Compose, guaranteeing a consistent, stable, and self-contained environment.

### Prerequisites

* **Docker:** (Engine and Docker Compose)
* **Web Browser**
* **Proxy Tool:** Burp Suite (highly recommended)
* **Listener:** A local machine Netcat listener.

### Launch Instructions

1.  **Clone the Repository:**
    ```bash
    git clone [YOUR REPO URL]
    cd [YOUR REPO FOLDER]
    ```

2.  **Launch the Stack:** This command builds the PHP image, starts the MySQL database, and creates all necessary networks and volumes. **Note: This will take 30-60 seconds for the database to initialize.**
    ```bash
    docker compose up --build -d
    ```

3.  **Access the Application:**
    * **Web Portal:** Access the application at `http://localhost:8080` (or the port specified in your `docker-compose.yml`).
    * **Register:** Create a new user account to begin the challenge.

---

## 🛑 Disclaimer

This environment is for **educational and authorized security testing purposes only.** Do not attempt to use any of the techniques found here against production systems.
