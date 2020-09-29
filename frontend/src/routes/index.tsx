import { RouteProps } from 'react-router-dom';
import Dashboard from '../pages/Dashboard';
import CategoryList from '../pages/category/PageList';
import MemberList from '../pages/member/PageList';
import GenreList from '../pages/genre/PageList';

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
        component: CategoryList,
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
        component: MemberList,
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
        component: GenreList,
        exact: true
    },
];

export default routes;