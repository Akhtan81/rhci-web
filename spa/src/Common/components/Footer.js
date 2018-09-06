import React from 'react'
import parameters from '../config/parameters'

class Footer extends React.Component {
    render() {
        return <div className="page-prefooter">
            <div className="container-fluid">
                <div className="row">
                    <div className="col-xs-6 col-sm-4 col-md-3 footer-block">
                        <h2>App</h2>
                        <p>v{Defaults.spa_version}</p>
                    </div>
                    <div className="col-xs-6 col-sm-4 col-md-3 footer-block">
                        <h2>Contacts</h2>
                        <p>{parameters.contacts.phone}</p>
                        <p>{parameters.contacts.email}</p>
                    </div>
                    <div className="col-xs-6 col-sm-4 col-md-3 footer-block">
                        <h2>Follow on social</h2>
                        <ul className="social-icons">
                            <li>
                                <a href={parameters.social.github.url}
                                   target="_blank" className="github"/>
                            </li>
                            <li>
                                <a href={parameters.social.linkedin.url}
                                   target="_blank" className="linkedin"/>
                            </li>
                            <li>
                                <a href={parameters.social.twitter.url}
                                   target="_blank" className="twitter"/>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    }
}

export default Footer