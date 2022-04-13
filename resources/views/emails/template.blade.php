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
    </style>
    <body>
        <img id="logo" src="{{ config('app.url') }}/logo.svg" />
        <div id="content">
            @yield('content')
        </div>
    </body>
</html>
