import * as React from 'react';
import { useParams } from 'react-router-dom';

import { Page } from "../../components/Page";
import { Form } from './Form';


const PageForm = () => {
    const { id } = useParams<any>();
    return (
        <Page title={!id ? 'Criar Categoria' : 'Editar Categoria'}>
            <Form />
        </Page >
    );
};

export default PageForm;