<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">


        <!-- jQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

        <title>API Test</title>


        <!-- bootstrap CSS only -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">

        <!-- Styles -->


        <style>
            body {
                
            }
        </style>
    </head>
    <body>



<div style='margin:20px;'>

    <h1>Programming Task Api Test</h1>


    <div style='margin-top: 20px;'>

        <button type="button" id="GET" httpType="GET" class="btn btn-outline-primary">Index (GET)</button>
        <button type="button" id="PUT" httpType="PUT" class="btn btn-outline-secondary">Store (PUT)</button>


    
    </div>

    <div style='margin-top:50px; background: lightgray;'>
        <span id="request"></span>
    </div>

    <div style='font-size: 200%;'>
       <span id="result"></span>
    </div>
    <div style='font-size: 200%;color: red;'>
        <span id="error"></span>
    </div>

</div>
    


    <script>
            let request = {};
            request.type = {
                "GET":{
                    "url":"api",
                    "stringify":false,
                },
                "PUT":{
                    "url":"api/store",
                    "stringify":true,
                }};

            request.data = {
                "requestId": 85261523,
                "customerId": 256945,
                "vehicle": {
                    "id": 9763928242,
                    "value": 11294,
                    "last_listed": null
                 }};



        // When document is ready listen for button click. If button clicked use button id attribute
        // in file path to pick the correct file
        $(document).ready(function(){
            $.ajaxSetup({ cache: false });
            $(".btn").click(function(){sendRequest(request,$(this).attr('httpType'));}); 
        });


        // send Json ajax request to api/calculate and display result if successful, else show error
        function sendRequest(request,httpType){
            $("#request").html(request.data);
            $.ajax({
                url: request.type[httpType].url,
                type: httpType,
                data: request.type[httpType].stringify ? JSON.stringify(request.data) : request.data,
                contentType: 'application/json; charset=utf-8',
                dataType: 'JSON',
                // processData: false,
                success: function(result) {
                   console.log(result);
                   $("#error").empty();
                   $("#result").html(result.vehicle.value);
                },
                error: function(xhr, ajaxOptions, thrownError){
                    console.log(xhr.status + ' ' + thrownError);
                   $("#result").empty();
                   $("#error").html(xhr.status + ' ' + thrownError);
                }
            });
        }


    </script>



               



       
    </body>
</html>
