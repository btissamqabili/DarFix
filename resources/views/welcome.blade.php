<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'DarFix') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="min-h-screen bg-[#f6f3eb] text-[#2F2926] antialiased">

        <!-- Header -->
        <header class="mx-auto flex max-w-[1440px] items-center justify-between px-5 py-6 sm:px-8 lg:px-12">

            <a href="{{ url('/') }}"
               aria-label="DarFix"
               class="flex items-center gap-3 font-serif text-2xl font-bold tracking-[-0.04em] text-[#8b1e1e]">

                <span aria-hidden="true"
                      class="flex h-9 w-9 items-center justify-center rounded-sm bg-[#8b1e1e] text-lg text-[#F6F3EB]">
                    ⚒
                </span>

                <span>DarFix</span>
            </a>

            <nav class="flex items-center gap-3 text-sm font-semibold sm:gap-6">

                <!-- Connexion -->
                <a href="{{ route('login') }}"
                   class="text-[#6F6862] transition hover:text-[#8b1e1e]">
                    Se connecter
                </a>

                <!-- Inscription -->
                @if(Route::has('register'))
                    <a href="{{ route('register') }}"
                       class="action-primary">
                        Créer un compte
                    </a>
                @endif

            </nav>
        </header>


        <!-- Main -->
        <main class="mx-auto max-w-[1440px] px-5 pb-12 sm:px-8 lg:px-12 lg:pb-20">

            <!-- Hero -->
            <section class="grid min-h-[620px] items-end gap-10 border-b border-[#e5dfd4] pb-10 pt-14 lg:grid-cols-[1.08fr_0.92fr] lg:pb-16 lg:pt-24">

                <div class="max-w-3xl">

                    <p class="eyebrow">
                        Le savoir-faire près de chez vous
                    </p>

                    <h1 class="mt-5 max-w-3xl font-serif text-5xl leading-[1.08] tracking-[-0.04em] sm:text-6xl lg:text-7xl">
                        Les projets de la maison méritent les bonnes mains.
                    </h1>

                    <p class="mt-7 max-w-xl text-lg leading-8 text-[#6F6862]">
                        DarFix met en relation les particuliers qui cherchent un bricoleur qualifié et les artisans locaux passionnés par le travail bien fait.
                    </p>

                    <div class="mt-9 flex flex-col gap-3 sm:flex-row">

                        <a href="{{ route('register') }}"
                           class="action-primary">
                            Publier une demande
                            <span class="ml-3">→</span>
                        </a>

                        <a href="{{ route('login') }}"
                           class="action-quiet">
                            Présenter mon savoir-faire
                        </a>

                    </div>

                </div>


                <!-- Hero Image -->
                <div class="relative flex min-h-[330px] flex-col justify-between overflow-hidden bg-[#8b1e1e] p-7 text-white sm:p-10 lg:min-h-[420px]">

                    <img src="{{ asset('images/heroes/artisan-workshop.jpg') }}"
                         alt="Artisan dans un atelier de bricolage"
                         class="absolute inset-0 h-full w-full object-cover opacity-35 mix-blend-screen">

                    <div class="absolute inset-0 bg-[#8b1e1e]/65"></div>

                    <span class="absolute -right-8 -top-8 font-serif text-[13rem] leading-none text-white/10">
                        ✳
                    </span>

                    <div class="relative flex items-center justify-between text-xs font-bold uppercase tracking-[0.2em] text-[#C9CAAC]">
                        <span>DarFix Maroc</span>
                        <span>01</span>
                    </div>

                    <div class="relative">

                        <p class="max-w-sm font-serif text-3xl leading-tight sm:text-4xl">
                            Un projet concret. Un artisan de confiance. Une relation locale sécurisée.
                        </p>

                        <p class="mt-6 max-w-sm text-sm leading-6 text-white/80">
                            De la première demande à la facture finale, chaque étape se déroule en toute sérénité.
                        </p>

                    </div>

                </div>

            </section>


            <!-- Compétences -->
            <section class="grid gap-8 py-12 lg:grid-cols-[0.8fr_1.2fr] lg:gap-16 lg:py-20">

                <div>

                    <p class="eyebrow">
                        Des compétences éprouvées
                    </p>

                    <h2 class="mt-4 max-w-md font-serif text-3xl leading-tight sm:text-4xl">
                        Du conseil initial au travail bien fini.
                    </h2>

                    <p class="mt-5 max-w-md text-sm leading-6 text-[#6F6862]">
                        Plomberie, électricité, peinture, menuiserie ou maçonnerie :
                        trouvez un bricoleur qualifié pour vos travaux.
                    </p>

                </div>


                <div class="grid grid-cols-2 gap-3 sm:gap-5">

                    <!-- Peinture -->
                    <figure class="group relative row-span-2 min-h-[300px] overflow-hidden bg-[#869B7E] sm:min-h-[390px]">

                        <img src="{{ asset('images/heroes/painting.jpg') }}"
                             alt="Travaux de peinture intérieure"
                             class="h-full w-full object-cover transition duration-700 group-hover:scale-105">

                        <figcaption class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-[#2F2926]/75 to-transparent p-5 pt-16 text-sm font-semibold text-white">
                            Peinture &amp; finitions
                        </figcaption>

                    </figure>


                    <!-- Menuiserie -->
                    <figure class="group relative min-h-[145px] overflow-hidden bg-[#C9CAAC] sm:min-h-[175px]">

                        <img src="{{ asset('images/heroes/artisan-workshop.jpg') }}"
                             alt="Travail de menuiserie dans un atelier"
                             class="h-full w-full object-cover transition duration-700 group-hover:scale-105">

                        <figcaption class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-[#2F2926]/75 to-transparent p-4 pt-10 text-sm font-semibold text-white">
                            Menuiserie &amp; agencement
                        </figcaption>

                    </figure>


                    <!-- Réparation -->
                    <figure class="group relative min-h-[145px] overflow-hidden bg-[#8b1e1e] sm:min-h-[175px]">

                        <img src="{{ asset('images/heroes/client-renovation.jpg') }}"
                             alt="Outils de rénovation et réparation"
                             class="h-full w-full object-cover transition duration-700 group-hover:scale-105">

                        <figcaption class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-[#2F2926]/75 to-transparent p-4 pt-10 text-sm font-semibold text-white">
                            Réparation &amp; maintenance
                        </figcaption>

                    </figure>

                </div>

            </section>


            <!-- Plateforme -->
            <section class="grid gap-8 border-t border-[#e5dfd4] py-12 lg:grid-cols-3 lg:gap-12 lg:py-20">

                <div>

                    <p class="eyebrow">
                        Une plateforme complète
                    </p>

                    <h2 class="mt-4 font-serif text-3xl leading-tight">
                        Conçue pour les clients et les prestataires.
                    </h2>

                </div>


                <!-- Client -->
                <div class="border-t border-[#e5dfd4] pt-5">

                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#869B7E]">
                        Pour les clients
                    </p>

                    <h3 class="mt-3 font-serif text-2xl">
                        Trouver le bon bricoleur.
                    </h3>

                    <p class="mt-3 text-sm leading-6 text-[#6F6862]">
                        Publiez votre mission, recevez des offres claires avec prix
                        et délais, et choisissez en toute confiance.
                    </p>

                    <a href="{{ route('register') }}"
                       class="mt-5 inline-block text-sm font-bold text-[#8b1e1e]">
                        Publier une demande →
                    </a>

                </div>


                <!-- Prestataire -->
                <div class="border-t border-[#e5dfd4] pt-5">

                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#869B7E]">
                        Pour les prestataires
                    </p>

                    <h3 class="mt-3 font-serif text-2xl">
                        Développer votre activité.
                    </h3>

                    <p class="mt-3 text-sm leading-6 text-[#6F6862]">
                        Présentez vos services, répondez aux missions disponibles
                        et valorisez votre savoir-faire grâce aux avis vérifiés.
                    </p>

                    <a href="{{ route('register') }}"
                       class="mt-5 inline-block text-sm font-bold text-[#8b1e1e]">
                        Créer un profil prestataire →
                    </a>

                </div>

            </section>

        </main>


        <!-- Footer -->
        <footer class="border-t border-[#e5dfd4] px-5 py-6 text-center text-xs text-[#6F6862] sm:px-8">
            DarFix · Plateforme de mise en relation entre clients et bricoleurs.
        </footer>

    </body>
</html>