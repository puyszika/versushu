<footer class="bg-gray-100 dark:bg-gray-900">
    <div class="relative mx-auto max-w-screen-xl px-4 py-16 sm:px-6 lg:px-8 lg:pt-24">
        <div class="lg:flex lg:items-end lg:justify-between">
            <div>
                <div class="flex justify-center text-teal-600 lg:justify-start dark:text-teal-300">
                    <img src="{{ asset('images/logo.png') }}" alt="Versus logó" class="h-10 w-auto inline-block">
                </div>

                <p
                    class="mx-auto mt-6 max-w-md text-center leading-relaxed text-gray-500 lg:text-left dark:text-gray-400"
                >
                    A weboldal magyar amatőr CS-játékosoknak készült, ahol a cél egy aktív, jófej közösség építése.
                    Játszhatsz barátokkal vagy versenyezhetsz eseményeinken – nálunk minden a játékról és a közösségről szól!
                </p>
            </div>

            <ul
                class="mt-12 flex flex-wrap justify-center gap-6 md:gap-8 lg:mt-0 lg:justify-end lg:gap-12"
            >
                <li>
                    <a
                        class="text-gray-700 transition hover:text-gray-700/75 dark:text-white dark:hover:text-white/75"
                        href="{{ route('info') }}"
                    >
                        Információk
                    </a>
                </li>

              <!--  <li>
                    <a
                        class="text-gray-700 transition hover:text-gray-700/75 dark:text-white dark:hover:text-white/75"
                        href="{{ route('contact') }}"
                    >
                        Kapcsolat
                    </a>
                </li> -->

                <li>
                    <a
                        class="text-gray-700 transition hover:text-gray-700/75 dark:text-white dark:hover:text-white/75"
                        href="{{ route('partners') }}"
                    >
                        Partnerek
                    </a>
                </li>

                <li>

                </li>
            </ul>
        </div>

        <p class="mt-12 text-center text-sm text-gray-500 lg:text-right dark:text-gray-400">
            Copyright &copy; 2025. VersusCS.hu
        </p>
    </div>
</footer>
