import React from 'react';
import {connect} from 'react-redux';
import {Link, withRouter} from 'react-router-dom';
import {MODEL_CHANGED} from '../actions';
import selectors from './selectors';
import Save from '../actions/Save';
import translator from '../../translations/translator';
import {setTitle} from "../../Common/utils";
import Logo from "../../Common/components/Logo";
import Lang from "../../Common/components/Lang";

class PasswordReset extends React.Component {

    componentWillMount() {
        setTitle(translator('navigation_reset_password'))
    }

    submit = () => {
        const {model} = this.props.PasswordReset

        this.props.dispatch(Save(model))
    }

    change = (key, value = null) => this.props.dispatch({
        type: MODEL_CHANGED,
        payload: {
            [key]: value
        }
    })

    changeString = name => e => this.change(name, e.target.value)

    getError = key => {
        const {errors} = this.props.PasswordReset.validator

        if (errors[key] === undefined) return null

        return <small className="d-block c-red-500 form-text text-muted">{errors[key]}</small>
    }

    render() {

        const {model, isLoading, isValid, isSaveSuccess, serverErrors} = this.props.PasswordReset

        return <div className="container">

            <div className="lang-container">
                <Lang/>
            </div>

            <Logo/>

            <div className="row">
                <div className="col-12 col-sm-8 col-md-6 offset-sm-2 offset-md-3">
                    <div className="card shadow-sm my-4">
                        <div className="card-body">

                            <h2 className="text-center">{translator('navigation_reset_password')}</h2>

                            <p>{translator('reset_password_remember')}
                                &nbsp;<Link to="/login">{translator('signin')}</Link></p>

                            {serverErrors.length > 0 && <div className="alert alert-danger">
                                <ul className="simple">{serverErrors.map((e, i) => <li key={i}>{e}</li>)}</ul>
                            </div>}

                            {isSaveSuccess && <div className="alert alert-success">
                                <div>{translator('password_reset_success_notice')}</div>
                            </div>}

                            <div className="row">
                                <div className="col-12">
                                    <div className="form-group">
                                        <label className="required">{translator('login')}</label>
                                        <input type="text"
                                               name="login"
                                               autoFocus={true}
                                               className="form-control"
                                               onChange={this.changeString('login')}
                                               value={model.login || ''}/>
                                        {this.getError('login')}
                                    </div>
                                </div>

                                <div className="col-12">
                                    <div className="form-group text-center">
                                        <button className="btn btn-success"
                                                onClick={this.submit}
                                                disabled={isLoading || !isValid}>
                                            <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-lock"}/>
                                            &nbsp;{translator('confirm')}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(PasswordReset))
