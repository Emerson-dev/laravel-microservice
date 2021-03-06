import { RouteProps } from 'react-router-dom';
import Dashboard from '../pages/Dashboard';
import CategoryList from '../pages/category/PageList';
import CategoryCreate from '../pages/category/PageForm';
import MemberList from '../pages/member/PageList';
import MemberCreate from '../pages/member/PageForm';
import GenreList from '../pages/genre/PageList';
import GenreCreate from '../pages/genre/PageForm';

export interface MyRouteProps extends RouteProps {
    name: string;
    label: string;
}

const routes: MyRouteProps[] = [
    {
        name: 'dashboard',
        label: 'Dashboard',
        path: '/',
        component: Dashboard,
        exact: true
    },
    {
        name: 'categories.list',
        label: 'Listar Categorias',
        path: '/categories',
        component: CategoryList,
        exact: true
    },
    {
        name: 'categories.create',
        label: 'Criar Categoria',
        path: '/categories/create',
        component: CategoryCreate,
        exact: true
    },
    {
        name: 'membes.list',
        label: 'Listar Membros',
        path: '/members',
        component: MemberList,
        exact: true
    },
    {
        name: 'membes.list',
        label: 'Criar Membro',
        path: '/members/create',
        component: MemberCreate,
        exact: true
    },
    {
        name: 'genres.list',
        label: 'Listar Genero',
        path: '/genres',
        component: GenreList,
        exact: true
    },
    {
        name: 'genres.list',
        label: 'Criar Genero',
        path: '/genres/create',
        component: GenreCreate,
        exact: true
    },
];

export default routes;