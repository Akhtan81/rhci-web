import React from 'react';
import {connect} from 'react-redux';
import {LOGIN_CREDENTIALS_CHANGED} from '../actions';
import selectors from './selectors';
import translator from '../../translations/translator';
import LoginCheck from '../actions/LoginCheck';

class Login extends React.Component {

    submit = () => {

        const {login, password} = this.props.Login

        this.props.dispatch(LoginCheck(login, password))
    }

    onChange = name => e => {
        this.props.dispatch({
            type: LOGIN_CREDENTIALS_CHANGED,
            payload: {
                [name]: e.target.value
            }
        })
    }

    render() {

        const {login, password, isValid, errors, isLoading} = this.props.Login

        return <div className="container">
            <div className="row">
                <div className="col col-sm-10 col-md-6 col-lg-4 offset-sm-1 offset-md-3 offset-lg-4 mx-auto">
                    <div className="card shadow-sm mt-4">
                        <div className="card-body">

                            <div className="text-center mb-2">
                                <img src="/img/favicon/apple-touch-icon-114x114.png" className="img-fluid mx-auto p-2"/>

                                <h4>{translator('login_title')}</h4>
                            </div>

                            {errors.length > 0 && <div className="alert-alert-danger">
                                <ul className="simple">{errors.map((e, i) => <li key={i}>{e}</li>)}</ul>
                            </div>}

                            <div className="form-group">
                                <input type="text"
                                       className="form-control text-center"
                                       name="login"
                                       onChange={this.onChange('login')}
                                       value={login || ''}/>
                            </div>
                            <div className="form-group">
                                <input type="password"
                                       className="form-control text-center"
                                       name="password"
                                       onChange={this.onChange('password')}
                                       value={password || ''}/>
                            </div>
                            <div className="form-group text-center">
                                <button className="btn btn-primary"
                                        onClick={this.submit}
                                        disabled={!isValid || isLoading}>
                                    <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-lock"}/>
                                    &nbsp;{translator('login_action')}
                                </button>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}

export default connect(selectors)(Login)
