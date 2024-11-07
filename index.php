<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Note</title>
    <style>
        /* Your existing CSS styles */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f4f4f9;
            margin: 0;
        }

        .form-container {
            width: 100%;
            max-width: 600px;
            background-color: #ffffff;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            border: 1px solid #ddd;
        }

        h1 {
            font-size: 36px;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        label {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
            display: block;
        }

        input[type="text"] {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            border: 2px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus {
            border-color: #4CAF50;
            outline: none;
        }

        input[type="submit"] {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            box-sizing: border-box;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .message {
            background-color: #e9f7e6;
            color: #4CAF50;
            border: 1px solid #4CAF50;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            font-size: 18px;
        }

        .view-notes-button {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-align: center;
            text-decoration: none;
            margin-top: 20px;
        }

        .view-notes-button:hover {
            background-color: #0056b3;
        }

        /* Add spacing between sections for better readability */
        .form-container > * {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h1>Add a New Note</h1>
        <form method="POST" action="home.php">
            <label for="name">Title:</label>
            <input type="text" name="name" id="name" required placeholder="Enter note title" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            <input type="submit" name="submit" value="Add Note">
        </form>

        <a href="home.php" class="view-notes-button">View All Notes</a>
    </div>
</body>
</html>
