<h1>500</h1>
<h3>Oops! Something went wrong on our server</h3>

<style>
    @import url('https://fonts.googleapis.com/css?family=Alfa+Slab+One|Josefin+Slab');

    body {
        background-color: #aeb6bf;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        overflow: hidden;
    }

    @keyframes glitch {
        0% { transform: translate(0); color: #566573; }
        20% { transform: translate(-5px, 5px); color: #ff4c4c; }
        40% { transform: translate(5px, -5px); color: #f1c40f; }
        60% { transform: translate(-5px, -5px); color: #ff4c4c; }
        80% { transform: translate(5px, 5px); color: #f1c40f; }
        100% { transform: translate(0); color: #17202a; }
    }

    h1 {
        font-family: 'Alfa Slab One', cursive;
        font-size: 150px;
        animation: glitch 1s infinite;
        text-align: center;
    }

    h3 {
        font-family: 'Josefin Slab', serif;
        font-size: 30px;
        text-align: center;
        margin-top: 20px;
        animation: glitch 1.5s infinite;
    }
</style><?php
