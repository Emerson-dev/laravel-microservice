import * as React from 'react';
import MUIDataTable, { MUIDataTableColumn } from 'mui-datatables';
import { useState, useEffect } from 'react';
import { httpVideo } from '../../util/http';
import { Chip } from '@material-ui/core';

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

type Props = {

};

const Table = (props: Props) => {

    const [data, setData] = useState([]);

    useEffect(() => {
        const fetchData = async () => {
            const result = await httpVideo.get('categories').then(response => response);

            setData(result.data.data);
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