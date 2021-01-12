import * as React from 'react';
import { Box, Button, ButtonProps, Checkbox, makeStyles, TextField, Theme, MenuItem } from '@material-ui/core';
import { useForm } from 'react-hook-form';
import { useState, useEffect } from 'react';
import categoryHttp from '../../util/http/category-http';
import genreHttp from '../../util/http/genre-http';


const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        },
        formControl: {
            marginTop: theme.spacing(1),
            minWidth: 120,
        }
    }
});

interface Category {
    id: string;
    name: string;
    is_active: boolean;
    created_at: string;

}

export const Form = () => {

    const classes = useStyles();

    const buttonProps: ButtonProps = {
        className: classes.submit,
        variant: "outlined"
    };

    const [categories, setCategories] = useState<any[]>([]);

    const { register, handleSubmit, getValues, setValue, watch } = useForm({
        defaultValues: {
            categories_id: []
        }
    });

    useEffect(() => {
        register({ name: "categories_id" })
    }, [register]);

    useEffect(() => {
        const fetchData = async () => {
            categoryHttp
                .list<{ data: Category[] }>()
                .then(({ data }) => setCategories(data.data));
        };
        fetchData();
    }, []);

    function onSubmit(formData: any, event: any) {
        genreHttp.create(formData)
            .then((response) => {
                console.log(response);
            });
    }


    return (
        <form onSubmit={handleSubmit(onSubmit)}>
            <TextField
                name="name"
                label="Nome"
                fullWidth
                variant={"outlined"}
                inputRef={register}
            />
            <TextField
                select
                name="categories_id"
                value={watch('categories_id')}
                label="Categorias"
                margin={'normal'}
                variant={"outlined"}
                fullWidth
                onChange={(e) => {
                    setValue('categories_id', e.target.value)
                }}
                SelectProps={{ multiple: true }}
            >
                <MenuItem value="" disabled>
                    <em>Selecione a categoria</em>
                </MenuItem>
                {
                    categories.map(
                        (category, key) => (
                            <MenuItem key={key} value={category.id}>{category.name}</MenuItem>
                        )
                    )
                }
            </TextField>

            <Checkbox
                name="is_active"
                inputRef={register}
                defaultChecked
            />
            Ativo?
            <Box dir={"rtl"}>
                <Button {...buttonProps} onClick={() => onSubmit(getValues(), null)}>Salvar</Button>
                <Button {...buttonProps} type="submit">Salvar e continuar editando</Button>
            </Box>
        </form>
    );
};
