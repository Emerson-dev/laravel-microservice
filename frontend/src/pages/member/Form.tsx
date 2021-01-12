import * as React from 'react';
import { Box, Button, ButtonProps, makeStyles, TextField, Theme, RadioGroup, FormLabel, FormControlLabel, FormControl, Radio } from '@material-ui/core';
import { useForm } from 'react-hook-form';
import memberHttp from '../../util/http/member-http';


const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
});

export const Form = () => {

    const defaultValues = {
        type: "1"
    };

    const classes = useStyles();

    const buttonProps: ButtonProps = {
        className: classes.submit,
        variant: "outlined"
    };

    const { register, handleSubmit, getValues, setValue } = useForm({
        mode: "onChange",
        defaultValues: {
            ...defaultValues
        }
    });

    const [typeValue, setType] = React.useState(defaultValues.type);

    function onSubmit(formData: any, event: any) {
        memberHttp.create(formData)
            .then((response) => {
                console.log(response);
            });
    }

    const handleChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        setType((event.target as HTMLInputElement).value);
        setValue("type", (event.target as HTMLInputElement).value);
    };

    return (
        <form onSubmit={handleSubmit(onSubmit)}>
            <TextField
                name="name"
                label="Nome"
                fullWidth
                variant={"outlined"}
                inputRef={register}
            />

            <FormControl component="fieldset" margin={"normal"}>
                <FormLabel component="legend">Tipo do membro</FormLabel>
                <RadioGroup aria-label="type" name="type" value={typeValue} onChange={handleChange}>
                    <FormControlLabel value="1" control={<Radio />} label="Diretor" inputRef={register} />
                    <FormControlLabel value="2" control={<Radio />} label="Ator" inputRef={register} />
                </RadioGroup>
            </FormControl>

            <Box dir={"rtl"}>
                <Button {...buttonProps} onClick={() => onSubmit(getValues(), null)}>Salvar</Button>
                <Button {...buttonProps} type="submit">Salvar e continuar editando</Button>
            </Box>
        </form>
    );
};
