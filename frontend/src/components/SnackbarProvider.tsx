import * as React from 'react';
import { SnackbarProvider as NotStackProvider, SnackbarProviderProps, WithSnackbarProps } from 'notistack';
import { IconButton, makeStyles, Theme } from '@material-ui/core';
import CloseIcon from '@material-ui/icons/Close';

const useStyles = makeStyles((theme: Theme) => {
    return {
        variantSuccess: {
            backgroundColor: theme.palette.success.main
        },
        variantError: {
            backgroundColor: theme.palette.error.main
        },
        variantInfo: {
            backgroundColor: theme.palette.info.main
        }
    }
});

export const SnackbarProvider: React.FC<SnackbarProviderProps> = (props) => {
    let snackbarProviderRef: WithSnackbarProps;
    const classes = useStyles();
    const defaultProps: any = {
        classes,
        autoHideDuration: 3000,
        maxSnack: 3,
        anchorOrigin: {
            horizontal: 'right',
            vertical: 'top'
        },
        ref: (el: any) => snackbarProviderRef = el,
        action: (key: any) => (
            <IconButton
                color={'inherit'}
                style={{ fontSize: 20 }}
                onClick={() => snackbarProviderRef.closeSnackbar(key)}
            >
                <CloseIcon />
            </IconButton>
        )
    };

    const newProps = { ...defaultProps, ...props };

    return (
        <NotStackProvider {...newProps}>
            {props.children}
        </NotStackProvider>
    );
};