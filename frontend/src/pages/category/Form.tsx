import * as React from 'react';
import {
    Box,
    Button,
    ButtonProps,
    Checkbox,
    FormControlLabel,
    makeStyles,
    TextField,
    Theme
} from '@material-ui/core';
import { useForm } from 'react-hook-form';
import { useHistory, useParams } from 'react-router-dom';
import categoryHttp from '../../util/http/category-http';
import { useSnackbar } from 'notistack';


const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
});

export const Form = () => {

    const classes = useStyles();

    const {
        register,
        handleSubmit,
        getValues,
        errors,
        reset,
        watch,
        setValue
    } = useForm<any>({
        defaultValues: {
            is_active: true
        }
    });

    const snackbar = useSnackbar();
    const history = useHistory();
    const { id } = useParams<any>();
    const [category, setCategory] = React.useState<any>(null);
    const [loading, setLoading] = React.useState<boolean>(false);

    const buttonProps: ButtonProps = {
        className: classes.submit,
        color: 'secondary',
        variant: "contained",
        disabled: loading
    };

    React.useEffect(() => {
        register({ name: 'is_active' })
    }, [register]);

    React.useEffect(() => {
        if (!id) {
            return;
        }
        setLoading(true);
        categoryHttp
            .get(id)
            .then(({ data }) => {
                setCategory(data.data);
                reset(data.data);
            })
            .finally(() => setLoading(false));
    }, [id, reset]);

    function onSubmit(formData: any, event: any) {
        setLoading(true);
        const http = !id
            ? categoryHttp.create(formData)
            : categoryHttp.update(category?.id, formData)

        http
            .then(({ data }) => {
                snackbar.enqueueSnackbar(
                    'Categoria salva com sucesso',
                    { variant: 'success' }
                );
                setTimeout(() => {
                    event
                        ? (
                            id
                                ? history.replace(`/categories/${data.data.id}/edit`)
                                : history.push(`/categories/${data.data.id}/edit`)
                        )
                        : history.push('/categories');
                });
            })
            .catch((error) => {
                console.log(error);
                snackbar.enqueueSnackbar(
                    'Erro ao salvar categoria',
                    { variant: 'error' }
                );
            })
            .finally(() => setLoading(false));
    }

    return (
        <form onSubmit={handleSubmit(onSubmit)}>
            <TextField
                name="name"
                label="Nome"
                fullWidth
                variant={"outlined"}
                inputRef={register({ required: 'Nome é requerido.' })}
                disabled={loading}
                error={errors?.name !== undefined}
                helperText={errors?.name?.message}
                InputLabelProps={{ shrink: true }}
            />
            <TextField
                name="description"
                label="Descrição"
                multiline
                rows="4"
                fullWidth
                variant={"outlined"}
                margin={"normal"}
                inputRef={register}
                disabled={loading}
                InputLabelProps={{ shrink: true }}
            />
            <FormControlLabel
                disabled={loading}
                control={
                    <Checkbox
                        color={'primary'}
                        name="is_active"
                        onChange={() => setValue('is_active', !getValues()['is_active'])}
                        checked={watch('is_active')}
                    />
                }
                label={'Ativo?'}
                labelPlacement={'end'}
            />
            <Box dir={"rtl"}>
                <Button {...buttonProps} onClick={() => onSubmit(getValues(), null)}>Salvar</Button>
                <Button {...buttonProps} type="submit">Salvar e continuar editando</Button>
            </Box>
        </form>
    );
};
