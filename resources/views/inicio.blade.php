<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Aerobooking</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .bg-avion {
            background-image: url('https://images.unsplash.com/photo-1504196606672-aef5c9cefc92');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>

<body class="bg-gray-900 text-white">

<!-- HERO -->
<div class="bg-avion h-screen flex items-center justify-center">

    <!-- overlay oscuro -->
    <div class="bg-black/60 w-full h-full flex items-center justify-center">

        <div class="text-center animate-fade-in">

            <h1 class="text-5xl md:text-6xl font-bold mb-6">
                ✈ Aerobooking
            </h1>

            <p class="mb-8 text-lg text-gray-300">
                Tu plataforma de gestión de vuelos y aerolíneas
            </p>

            <!-- BOTONES -->
            <div class="flex gap-4 justify-center">

                <a href="/login"
                   class="bg-blue-600 px-6 py-3 rounded-lg text-lg hover:bg-blue-700 transition">
                    Iniciar Sesión
                </a>

                <a href="/register"
                   class="bg-green-500 px-6 py-3 rounded-lg text-lg hover:bg-green-600 transition">
                    Registrarse
                </a>

            </div>

        </div>
    </div>

</div>

<!-- ANIMACIÓN -->
<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px);}
    to { opacity: 1; transform: translateY(0);}
}

.animate-fade-in {
    animation: fadeIn 1.5s ease;
}
</style>

</body>
</html>