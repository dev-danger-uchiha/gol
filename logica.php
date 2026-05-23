<?php
session_start();

// Si por alguna razón no hay sesión, inicializamos en 0
if (!isset($_SESSION['player_score'])) {
    $_SESSION['player_score'] = 0;
    $_SESSION['cpu_score'] = 0;
    $_SESSION['rounds'] = 0;
}

$message = "¡Toma el balón y elige a dónde patear!";
$message_color = "text-gray-300";
$game_over = false;
$ball_class = "top-3/4 left-1/2 -translate-x-1/2 -translate-y-1/2"; // Posición inicial: Punto Penal

// Lógica de la jugada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['direction'])) {
    $player_choice = $_POST['direction'];
    $directions = ['izquierda', 'centro', 'derecha'];
    
    // La CPU (portero) elige un lado al azar
    $cpu_choice = $directions[array_rand($directions)];
    
    $_SESSION['rounds']++;

    if ($player_choice === $cpu_choice) {
        $message = "¡ATAJADÓN! El portero adivinó a la $cpu_choice.";
        $message_color = "text-red-400";
        $_SESSION['cpu_score']++;
        $ball_class = "ball-saved-$player_choice"; // Simular atajada
    } else {
        $message = "¡GOOOOOL! La pusiste a la $player_choice.";
        $message_color = "text-green-400 font-bold";
        $_SESSION['player_score']++;
        $ball_class = "ball-goal-$player_choice"; // Simular gol
    }

    // Comprobar si alguien ganó el partido (El primero en 5)
    if ($_SESSION['player_score'] >= 5 || $_SESSION['cpu_score'] >= 5) {
        $game_over = true;
        if ($_SESSION['player_score'] >= 5) {
            $message = "¡FIN DEL PARTIDO! Has ganado🏆";
            $message_color = "text-yellow-400 text-3xl font-black";
        } else {
            $message = "¡FIN DEL PARTIDO! Has perdido❌";
            $message_color = "text-red-500 text-3xl font-black";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanda de Penales - ¡En la Cancha!</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Animaciones para el balón */
        .football-ball {
            transition: all 0.6s ease-out;
        }

        /* Trayectorias de GOL */
        .ball-goal-izquierda { top: 15%; left: 15%; transform: scale(0.6); }
        .ball-goal-centro    { top: 10%; left: 50%; transform: scale(0.6) translateX(-50%); }
        .ball-goal-derecha   { top: 15%; left: 85%; transform: scale(0.6); }

        /* Trayectorias de ATAJADA (el balón va hacia afuera o es detenido) */
        .ball-saved-izquierda { top: 15%; left: 10%; transform: scale(0.6) rotate(-45deg); opacity: 0.8; }
        .ball-saved-centro    { top: 10%; left: 50%; transform: scale(0.6) translateX(-50%) translateY(10px); opacity: 0.8; }
        .ball-saved-derecha   { top: 15%; left: 90%; transform: scale(0.6) rotate(45deg); opacity: 0.8; }
    </style>
</head>
<body class="bg-gray-950 flex items-center justify-center min-h-screen text-white font-sans p-4">
    
    <div class="bg-gray-900 p-8 rounded-3xl shadow-2xl max-w-4xl w-full border border-gray-800">
        
        <!-- Marcador Profesional -->
        <div class="flex justify-between items-center mb-8 bg-gray-950 p-6 rounded-2xl border-4 border-green-900 shadow-inner">
            <div class="text-center w-1/3">
                <p class="text-sm text-gray-400 uppercase tracking-widest font-bold mb-1">LOCAL</p>
                <p class="text-6xl font-black text-green-500">
                    <?= str_pad($_SESSION['player_score'], 2, "0", STR_PAD_LEFT) ?>
                </p>
            </div>
            <div class="text-center w-1/3 border-x-4 border-green-900">
                <p class="text-xs text-gray-500 uppercase tracking-widest font-bold mb-1">RONDA</p>
                <p class="text-3xl font-bold text-gray-300"><?= $_SESSION['rounds'] ?></p>
            </div>
            <div class="text-center w-1/3">
                <p class="text-sm text-gray-400 uppercase tracking-widest font-bold mb-1">VISITA</p>
                <p class="text-6xl font-black text-red-500">
                    <?= str_pad($_SESSION['cpu_score'], 2, "0", STR_PAD_LEFT) ?>
                </p>
            </div>
        </div>

        <!-- SIMULACIÓN DE LA CANCHA Y PORTERÍA -->
        <div class="relative bg-green-700 w-full h-[400px] rounded-2xl border-4 border-white/20 shadow-lg overflow-hidden mb-8">
            
            <!-- Pasto (Líneas de la cancha) -->
            <div class="absolute inset-0 flex flex-col">
                <?php for($i=0; $i<8; $i++): ?>
                    <div class="w-full h-1/2 <?= $i % 2 === 0 ? 'bg-green-600' : 'bg-green-700' ?>"></div>
                <?php endfor; ?>
            </div>

            <!-- La Portería (Simulada con bordes y sombra) -->
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[90%] h-[35%] bg-white/10 border-t-8 border-l-8 border-r-8 border-white rounded-t-md shadow-2xl">
                <div class="absolute inset-0 bg-gray-900/40"></div> <!-- Red de la portería -->
            </div>

            <!-- Punto Penal -->
            <div class="absolute top-[75%] left-1/2 -translate-x-1/2 w-4 h-4 bg-white rounded-full shadow-md"></div>

            <!-- EL BALÓN (Elemento animado) -->
            <div class="absolute football-ball w-16 h-16 bg-white rounded-full shadow-2xl flex items-center justify-center border-4 border-gray-900 z-10 <?= $ball_class ?>">
                <!-- Gráfico simple de balón -->
                <span class="text-4xl">⚽</span>
            </div>
        </div>

        <!-- Área de Mensajes -->
        <div class="mb-8 min-h-[90px] flex items-center justify-center text-center bg-gray-950 p-6 rounded-2xl border border-gray-800shadow-inner relative z-20">
            <p class="text-xl font-medium <?= $message_color ?>"><?= $message ?></p>
        </div>

        <!-- Controles de Tiro -->
        <?php if (!$game_over): ?>
            <form method="POST" class="grid grid-cols-3 gap-4 mb-6 relative z-20">
                <button type="submit" name="direction" value="izquierda" class="bg-green-600 hover:bg-green-500 text-white font-extrabold py-6 rounded-2xl shadow-xl transition duration-150 transform hover:-translate-y-1 active:scale-95 text-lg">
                    ◀ IZQUIERDA
                </button>
                <button type="submit" name="direction" value="centro" class="bg-green-600 hover:bg-green-500 text-white font-extrabold py-6 rounded-2xl shadow-xl transition duration-150 transform hover:-translate-y-1 active:scale-95 text-lg">
                    CENTRO
                </button>
                <button type="submit" name="direction" value="derecha" class="bg-green-600 hover:bg-green-500 text-white font-extrabold py-6 rounded-2xl shadow-xl transition duration-150 transform hover:-translate-y-1 active:scale-95 text-lg">
                    DERECHA ▶
                </button>
            </form>
        <?php else: ?>
            <div class="mb-6 relative z-20">
                <a href="index.php" class="block w-full text-center bg-yellow-500 hover:bg-yellow-400 text-gray-950 font-black py-5 rounded-2xl shadow-2xl transition duration-200 text-xl transform hover:scale-105">
                    Jugar la Revancha
                </a>
            </div>
        <?php endif; ?>

        <div class="text-center mt-6">
            <a href="index.php" class="text-gray-600 hover:text-white text-sm font-semibold transition duration-200">
                Abandonar partido y volver al inicio
            </a>
        </div>
    </div>

</body>
</html>
