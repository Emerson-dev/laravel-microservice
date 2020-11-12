import * as React from 'react';
import MUIDataTable, { MUIDataTableColumn } from 'mui-datatables';
import { useState, useEffect } from 'react';
import { Chip } from '@material-ui/core';
import categoryHttp from '../../util/http/category-http';

const options = { year: "numeric", month: "long", day: "numeric" };
const columnsDefinition: MUIDataTableColumn[] = [
    {
        name: "name",
        label: "Nome"
    },
    {
        name: "is_active",
        label: "Ativo?",
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                return value ? <Chip label="Sim" color="primary" /> : <Chip label="Nao" color="secondary" />;
            }
        }
    },
    {
        name: "created_at",
        label: "Criado em",
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                const date = new Date(value);
                date.toLocaleDateString("pt-br", options);
                return date.toLocaleDateString("pt-br", { ...options, month: 'numeric' });
            }
        }
    }
];

interface Category {
    id: string;
    name: string;
    is_active: boolean;
    created_at: string;

}

type Props = {

};

const Table = (props: Props) => {

    const [data, setData] = useState<Category[]>([]);

    useEffect(() => {
        const fetchData = async () => {
            categoryHttp
                .list<{ data: Category[] }>()
                .then(({ data }) => setData(data.data));
        };
        fetchData();
    }, []);

    return (
        <MUIDataTable
            title="Listagem de categorias"
            columns={columnsDefinition}
            data={data}
        />
    );
};

export default Table;