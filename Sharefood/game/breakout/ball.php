<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0" />
    <title>Share Food</title>
    <link rel="icon" href="img/logo.png">
    <!--  reference link to font awesome  -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    
  </head>
  <body>

<canvas id="myCanvas" width="300" height="300" style="border:1px solid black"></canvas>
<br><button id="restart">Restart</button>

<script>

$(document).ready(function(){

  gameStart();

    $("#restart").click(function(event){
        gameStart();

    });

  function gameStart() {

    var canvas = document.getElementById("myCanvas");
    var context = canvas.getContext("2d");
    //ball attributes
    var x = canvas.width/2; //set start ball x-axis to middle
    var y = canvas.height-30; //set start ball y-axis 30px above bottom edge
    var dx = 5; //movement along x per frame
    var dy = -5; //movement along y per frame
    var ballRadius = 8;
    var color = randomColor();
    
    var paddleWidth = 80;
    var paddleHeight = 10;
    var paddleX = (canvas.width-paddleWidth)/2; //starting point on x-axis
    
    var rightPress = false;
    var leftPress = false;
    
    var brickRowCount = 8;
    var brickColumnCount = 8;
    var brickWidth = 34;
    var brickHeight = 12;
    var brickPadding = 0; //space between bricks
    var brickOffsetTop = 44;
    var brickOffsetLeft = 16;

    var score = 0;

    // draw canvas background
    function background() {
        context.beginPath();
        context.rect(0, 0, 680, 400);
        context.fillStyle = "#000000";
        context.fill();
        context.closePath();
    }

    // creating an empty array to store the bricks
    var bricks = [];
    for(var c=0; c<brickColumnCount; c++) {
        bricks[c] = [];
        for(var r=0; r<brickRowCount; r++) {
            bricks[c][r] = {x: 0, y: 0, status: 1} // set status attribut to 1
        }
    }
    
    // draw bricks in each matrix element
    function drawBricks() {
        var img = new Image();
        img.src = "img/strawberry.png";

        for(var c=0; c<brickColumnCount; c++) {
            for(var r=0; r<brickRowCount; r++) {
                if(bricks[c][r].status == 1) { // if status attribute is 1 = draw, if 0 = don't draw
                    var brickX = (c*(brickWidth + brickPadding)) + brickOffsetLeft;
                    var brickY = (r*(brickHeight + brickPadding)) + brickOffsetTop;
                    bricks[c][r].x = brickX;
                    bricks[c][r].y = brickY;
                    context.beginPath();
                    //context.rect(brickX, brickY, brickWidth, brickHeight);
                    context.fillStyle = context.createPattern(img, 'repeat');
                    context.fillRect(brickX, brickY, brickWidth, brickHeight);
                    //context.fill
                    //context.fillStyle = "#0095DD";
                    //context.fill();
                    context.closePath();
                }
            }
        }
    }

    // random color generator
    function randomColor() {
        var values = '468ACE'.split(''); //stores an array of values
        var color = '#';
        for(var i = 0; i < 6; i++) { //assigns either 4,6,8,A,C,E to each place value of hex color
            color += values[Math.floor(Math.random() * values.length)];
        }
        return color;
    }

    // draw the ball
    function drawBall() {
        context.beginPath();
        context.arc(x, y, ballRadius, 0, Math.PI*2); // x, y, arc radius, start angle, end angle
        context.fillStyle = color; //stores color, red
        context.fill(); //paints circle
        context.closePath();
    }

    // draw the paddle
    function drawPaddle() {
        context.beginPath();
        context.rect(paddleX, canvas.height-paddleHeight, paddleWidth, paddleHeight);
        context.fillStyle = '#ffffff';
        context.fill();
        context.closePath();
    }

    document.addEventListener("keydown", keyDownHandler, false);
    document.addEventListener("keyup", keyUpHandler, false);
    document.addEventListener("mousemove", mouseMoveHandler, false);
    document.addEventListener("touchmove", touchMoveHandler);

    function keyDownHandler(e) {
        if(e.keyCode == 39) {
            rightPress = true;
        } 
        else if(e.keyCode == 37) {
            leftPress = true;
        }
    }

    function keyUpHandler(e) {
        if(e.keyCode == 39) {
            rightPress = false;
        } 
        else if(e.keyCode == 37) {
            leftPress = false;
        }
    }

    function mouseMoveHandler(e) {
        let relativeX = e.clientX - canvas.offsetLeft; //distance b/t left canvas and mouse pointer
        //if pointer is within canvas boundary
        if(relativeX > (paddleWidth/2) && relativeX+(paddleWidth/2) < canvas.width) { 
            paddleX = relativeX - paddleWidth/2;  //movement relative to middle of paddle
        }
    }

    function touchMoveHandler(e) {
        paddleX = e.touches[0].screenX - paddleWidth;
    }
    
    // detecting if ball is colliding with brick and setting status to 0 if true
    function collision() {
        for(var c=0; c<brickColumnCount; c++) {
            for(var r=0; r<brickRowCount; r++) {
                let b = bricks[c][r];
                if(b.status == 1) {
                    if(x  > b.x && x < b.x + brickWidth && y > b.y && y < b.y + brickHeight) {
                        dy = -dy;
                        color = randomColor();
                        b.status = 0;
                        score += 5;
                        if(score/5 == brickRowCount * brickColumnCount) {
                            context.fillText("YOU WIN! YOUR SOCRE IS " + score, 50, 200);
                            throw new Error("This is not an error. Game over!");
                            
                        }
                    }
                }
            }
        }
    }

    function drawScore() {
        context.font = "12px Arial";
        context.fillStyle = "#ffffff";
        context.fillText("Score: " + score, 8, 20); //text coordinates
    }

    // clear canvas and redraw ball
    function draw() {
        context.clearRect(0, 0, canvas.width, canvas.height);
        background();
        drawBricks();
        drawBall();
        drawPaddle();
        drawScore();
        collision();
        if(y + dy < ballRadius) { //ball collision with top screen, change direction and color
            dy = -dy;
            color = randomColor();
        } 
        else if(y + dy > canvas.height-paddleHeight) {  //bounce off paddle
            if(x > paddleX && x < paddleX + paddleWidth) { // while within paddle width
                dy = -dy;
            } 
            else {
                context.fillText("GAME OVER! YOUR SCORE IS: " + score, 50, 200);
                throw new Error("This is not an error. Game over!");
            }
        }
        if(x + dx > canvas.width-ballRadius || x + dx < ballRadius) { // ball bounces of left right walls
            dx = -dx;
            color = randomColor();
        }
        if(rightPress && paddleX < canvas.width-paddleWidth) { //contains paddle within right boundary
            paddleX += 5;
        } 
        else if (leftPress && paddleX > 0) {  //contains paddle within left boundary
            paddleX -= 5;
        }
        // movement of ball per frame refresh
        x += dx;
        y += dy;
        requestAnimationFrame(draw);
    }
    draw();

  }

});

</script>