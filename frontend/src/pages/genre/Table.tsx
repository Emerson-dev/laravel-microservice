import * as React from 'react';
import MUIDataTable, { MUIDataTableColumn } from 'mui-datatables';
import { useState, useEffect } from 'react';
import { httpVideo } from '../../util/http';
import { BadgeNo, BadgeYes } from '../../components/Badge';

const options = { year: "numeric", month: "long", day: "numeric" };
const columnsDefinition: MUIDataTableColumn[] = [
    {
        name: "name",
        label: "Nome"
    },
    {
        name: "categories",
        label: "Categorias",
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                const categories = value.map((category: any, index: number) => {
                    if (value.length === index + 1) {
                        return category.name;
                    } else {
                        return category.name + ', ';
                    }
                });

                return categories;
            }
        }
    },
    {
        name: "is_active",
        label: "Ativo?",
        options: {
            customBodyRender(value, tableMeta, updateValue) {
                return value ? <BadgeYes/> : <BadgeNo/>;
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
            const result = await httpVideo.get('genres').then(response => response);
            setData(result.data.data);
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