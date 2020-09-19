import * as React from 'react';
import MUIDataTable, { MUIDataTableColumn } from 'mui-datatables';
import { useState, useEffect } from 'react';
import { httpVideo } from '../../util/http';
import { TypeMemberEnum } from './enum/typeMemberEnum';

const options = { year: "numeric", month: "long", day: "numeric" };
const columnsDefinition: MUIDataTableColumn[] = [
    {
        name: "name",
        label: "Nome"
    },
    {
        name: "type",
        label: "Tipo",
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                return TypeMemberEnum[value];
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
            const result = await httpVideo.get('cast_members').then(response => response);

            setData(result.data);
        };
        fetchData();
    }, []);

    return (
        <MUIDataTable
            title="Listagem de membros do elenco"
            columns={columnsDefinition}
            data={data}
        />
    );
};

export default Table;