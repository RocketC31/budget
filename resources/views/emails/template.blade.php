<!DOCTYPE html>
<html>
    <style>
        body {
            margin: 0;
            box-sizing: border-box;
            height: 100vh;
            font-family: Roboto, Arial, Helvetica, sans-serif;
        }

        #content {
            padding:10px;
            width: 90%;
            max-width: 700px;
            margin: auto;
            text-align: center;
        }

        #logo {
            height: 40px;
            width:100%;
            margin: 20px auto;
        }

        .button {
            padding: 10px 20px;
            display: block;
            text-decoration: none;
            font-size: 12px;
            max-width: 300px;
            margin: 10px auto;
            font-weight: 600;
            color: #FFF;
            background-color: #4299E1;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin: 10px;
        }

        table td, table th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #4299E1;
            color: white;
        }
    </style>
    <body>
        <img id="logo" src="{{ config('app.url') }}/logo.svg" />
        <div id="content">
            @yield('content')
        </div>
    </body>
</html>
