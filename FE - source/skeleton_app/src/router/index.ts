import { createRouter, createWebHistory } from 'vue-router'
import type { BehaviorSubject } from 'rxjs'

// pages 
import HomeView from '@/views/HomeView.vue'
import LoginView from '@/views/LoginView.vue'
import RegisterView from '@/views/RegisterView.vue'
import ProfileView from '@/views/ProfileView.vue'
import BookListVIew from '@/views/BookListVIew.vue'
import RaceView from '@/views/RaceView.vue'


const initRouter = (currentUser$: BehaviorSubject<string | null>) => {

  const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
      {
        path: '/',
        name: 'home',
        component: HomeView,
      },
      {
        path: '/about',
        name: 'about',
        // route level code-splitting
        // this generates a separate chunk (About.[hash].js) for this route
        // which is lazy-loaded when the route is visited.
        component: () => import('../views/AboutView.vue'),
      },
      {
        path: '/login',
        name: 'login',
        component: LoginView,
      },
      {
        path: '/register',
        name: 'register',
        component: RegisterView,
      },
      {
        path: '/profile',
        name: 'profile',
        component: ProfileView,
        meta: { requiresAuth: true }
      },
      {
        path: '/book',
        name: 'book',
        component: BookListVIew,
        meta: { requiresAuth: true }
      },
      {
        path: '/race',
        name: 'race',
        component: RaceView,
        meta: { requiresAuth: true }
      }
    ],

  })


  router.beforeEach((to, from, next) => {

    const user = currentUser$.value;

    if (to.matched.some(record => record.meta.requiresAuth)) {

      if (!user) {
        return next({
          path: '/login',
          query: { redirect: to.fullPath }
        })
      }

    } else if ((to.path === '/login' || to.path === '/register') && user) {
      return next({ path: '/' })
    }
    return next()

  });

  return router;
}
export default initRouter;
