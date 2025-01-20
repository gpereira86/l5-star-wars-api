# Implemented Improvements

During the development of the project, the following improvements were implemented with the aim of enhancing functionality, security, and user experience:

## 1. Fetching Movie Poster via API
- **Description**: The system can now automatically fetch the movie poster from an external API.
- **Objective**: Improve the visual presentation of the movie by displaying the poster image along with the movie data.
- **Benefit**: The user can view the movie poster without needing to manually insert it, making the system more dynamic.

## 2. Adding Query for Redirecting to the Movie Trailer on YouTube
- **Description**: A feature was added to generate a query that directs the user to the official movie trailer on YouTube.
- **Objective**: Facilitate direct access to the movie trailer without the user needing to search manually.
- **Benefit**: Enhanced user experience by providing immediate access to the trailer with a single click.

## 3. Password-Protected Route for Retrieving Logs from the Database
- **Description**: A secure, password-protected route (API key) was created, allowing the retrieval of logs stored in the database.
- **Objective**: Ensure that only authorized users can access sensitive information, such as system logs.
- **Benefit**: Increased security in managing system data, preventing unauthorized access to confidential information.

---
These improvements were implemented to make the system more efficient, secure, and user-friendly, providing an enhanced experience for users and better control for administrators.
-
> **Note:** The software is scalable in terms of integration with external APIs. For potential changes, polymorphism may be required, but in general, it is possible to replace the API data provider without significant difficulties.