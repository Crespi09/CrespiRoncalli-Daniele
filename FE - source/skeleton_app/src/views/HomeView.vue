<template>
  <div class="relative min-h-[40rem] lg:min-h-0 bg-surface-0 dark:bg-surface-900 flex lg:flex-row flex-col">
      <div class="flex lg:flex lg:flex-row flex-col justify-center md:justify-normal h-full flex-1">
          <div class="relative flex-1 z-20 flex items-center justify-center">
              <div class="flex items-center justify-center h-full">
                  <div class="w-full max-w-2xl px-6 py-12 lg:p-12 xl:p-16 text-center lg:text-left">
                      <h1 class="text-4xl xl:text-5xl font-bold text-surface-0 lg:text-surface-900 dark:text-surface-0 mb-6 !leading-tight">
                          <span class="block">Home Page</span>
                          <!-- <span class="block text-primary">todo app</span> -->
                      </h1>

                      <p class="text-surface-0/90 lg:text-surface-700 dark:text-surface-200 text-xl leading-normal mb-8 max-w-xl lg:max-w-none">
                        Gara Podistica
                      </p>

                      <!-- <div class="flex items-center gap-4 justify-center lg:justify-start">
                          <RouterLink to="/about">
                            <Button label="About Us" type = "button" outlined />
                        </RouterLink>

                      </div>
                      -->
                  </div>
              </div>
          </div>

          <div class="absolute lg:relative inset-0 lg:inset-auto flex-1">
              <div class="absolute lg:hidden inset-0 bg-surface-900/60 dark:bg-surface-900/80 z-10" />
              <img src="/imgs/runner.jpg" alt="hero-1" class="h-full w-full object-cover xl:[clip-path:polygon(12%_0,100%_0%,100%_100%,0_100%)]" />
          </div>
      </div>
  </div>

</template>

<script setup>
import Button from 'primevue/button';
</script>
