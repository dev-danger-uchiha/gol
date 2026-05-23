<?php
session_start();

if (!isset($_SESSION['player_score'])) {
    $_SESSION['player_score'] = 0;
    $_SESSION['cpu_score'] = 0;
    $_SESSION['rounds'] = 0;
}

$message = "Elige a dónde patear tu penal.";
$message_color = "text-gray-300";
$game_over = false;

// Lógica de la jugada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['direction'])) {
    $player_choice = $_POST['direction'];
    $directions = ['izquierda', 'centro', 'derecha'];
    
    // La CPU (portero) elige un lado al azar
    $cpu_choice = $directions[array_rand($directions)];
    
    $_SESSION['rounds']++;

    if ($player_choice === $cpu_choice) {
        $message = "¡Atajadón! El portero voló hacia la $cpu_choice y detuvo tu tiro.";
        $message_color = "text-red-400";
        $_SESSION['cpu_score']++;
    } else {
        $message = "¡GOOOOOL! La pusiste a la $player_choice y el portero se tiró al lado contrario.";
        $message_color = "text-green-400 font-bold";
        $_SESSION['player_score']++;
    }

    // Comprobar si alguien ganó el partido (El primero en 5)
    if ($_SESSION['player_score'] >= 5 || $_SESSION['cpu_score'] >= 5) {
        $game_over = true;
        if ($_SESSION['player_score'] >= 5) {
            $message = "¡FIN DEL PARTIDO! Has ganado la tanda de penales 🏆";
            $message_color = "text-yellow-400 text-2xl font-black";
        } else {
            $message = "¡FIN DEL PARTIDO! El portero rival es el héroe. Has perdido ❌";
            $message_color = "text-red-500 text-2xl font-black";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cobro de Penal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen text-white font-sans">
    
    <div class="bg-gray-800 p-8 rounded-2xl shadow-2xl max-w-lg w-full border-t-8 border-green-500">
        
        <!-- Marcador -->
        <div class="flex justify-between items-center mb-8 bg-gray-900 p-5 rounded-xl border border-gray-700 shadow-inner">
            <div class="text-center w-1/3">
                <p class="text-xs text-gray-500 uppercase tracking-widest font-bold mb-1">Tú</p>
                <p class="text-5xl font-black text-green-500"><?= $_SESSION['player_score'] ?></p>
            </div>
            <div class="text-center w-1/3 border-x border-gray-700">
                <p class="text-xs text-gray-500 uppercase tracking-widest font-bold mb-1">Ronda</p>
                <p class="text-2xl font-bold text-gray-300"><?= $_SESSION['rounds'] ?></p>
            </div>
            <div class="text-center w-1/3">
                <p class="text-xs text-gray-500 uppercase tracking-widest font-bold mb-1">Portero</p>
                <p class="text-5xl font-black text-red-500"><?= $_SESSION['cpu_score'] ?></p>
            </div>
        </div>

        <!-- Área de Mensajes de la jugada -->
        <div class="mb-8 min-h-[80px] flex items-center justify-center text-center bg-gray-700/30 p-4 rounded-lg">
            <p class="text-lg <?= $message_color ?>"><?= $message ?></p>
        </div>

        <!-- Controles -->
        <?php if (!$game_over): ?>
            <form method="POST" class="grid grid-cols-3 gap-3 mb-6">
                <button type="submit" name="direction" value="izquierda" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-5 rounded-xl shadow-lg transition duration-200 transform hover:-translate-y-1">
                    ◀ Izq
                </button>
                <button type="submit" name="direction" value="centro" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-5 rounded-xl shadow-lg transition duration-200 transform hover:-translate-y-1">
                    Centro
                </button>
                <button type="submit" name="direction" value="derecha" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-5 rounded-xl shadow-lg transition duration-200 transform hover:-translate-y-1">
                    Der ▶
                </button>
            </form>
        <?php else: ?>
            <div class="mb-6">
                <a href="index.php" class="block w-full text-center bg-green-500 hover:bg-green-400 text-white font-bold py-4 rounded-xl shadow-lg transition duration-200">
                    Jugar la Revancha
                </a>
            </div>
        <?php endif; ?>

        <div class="text-center mt-6">
            <a href="index.php" class="text-gray-500 hover:text-white text-sm font-semibold transition duration-200">
                Abandonar partido y volver al inicio
            </a>
        </div>
    </div>

</body>
</html>