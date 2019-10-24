import React from 'react';
import {Link, withRouter} from 'react-router-dom';
import translator from '../../translations/translator';
import {setTitle} from "../../Common/utils";
import Logo from "../../Common/components/Logo";

class RegisterIntroduction extends React.Component {

    componentWillMount() {
        setTitle(translator('navigation_partners_register'))
    }

    render() {

        return <div className="container">

            <Logo/>

            <div className="row">
                <div className="col-12 col-md-10 col-lg-8 offset-md-1 offset-lg-2">
                    <div className="card shadow-sm my-4">
                        <div className="card-body">

                            <h3 className="text-center mb-4">Do you live to make a difference in people’s lives? Are you passionate about helping the environment?</h3>
                            <h6 className="text-center mb-4">
                                We invite you to be a part of the revolutionary new technological project which allows you to earn a living!
                                Be one of the TaBee team and make a significant impact to reduce landfill waste and clean your neighbourhood!
                            </h6>

                            <h3>How does it work?</h3>
                            <p>The primary goals of the TaBee application are to make people’s lives easier by automating the recycling process and to contribute to preventing climate change.</p>
                            <p>Whether you are a single driver or a small company which is struggling to find customers, TaBee provides you with a list of clients that are waiting for you to pick up their stuff. The app users select the necessary service <b>Junk removal/Shredding/Donation</b>, point out the pickup time and place, and you take the stuff out for further recycling. TaBee gives you an effective solution to promote your company and have a great number of customers without any marketing expenses.</p>

                            <h3>Products you can pick up for recycling</h3>
                            <p className="font-weight-bold">Junk</p>
                            <p>You collect furniture and household appliances for further recycling and there is a high number of requests at the moment, as the service is several times cheaper for the TaBee users compared to junk removal companies offer.</p>
                            <p className="font-weight-bold">Waste paper</p>
                            <p>You shred paper documents, and there is a high number of requests at the moment as the service is several times cheaper for the TaBee users compared to shredding companies offer.</p>
                            <p className="font-weight-bold">Donation stuff</p>
                            <p>As a charity organisation, you will be able to see the list of stuff that people would like to donate. Once you accept a donation you can collect the load. The service is free of charge for both donors and charity organisations.</p>

                            <h3>What do you need?</h3>
                            <ul>
                                <li>be registered as an entrepreneur or a charity organisation</li>
                                <li>have a Stripe payment system account</li>
                                <li>have a decent size car</li>
                            </ul>
                            <p>The logistic process is perfectly set and simple, and we are looking for partners who would work as responsibly as bees to help the Planet. We aim to revolutionise the recycling process all over the world the same as taxi service apps have taken over the ridesharing industry! You can be the first in your area and the one who makes this revolution! Our collector partners set their own price for the service and are paid either monthly or annually. The TaBee commission is only 7% of the transaction.</p>


                            <div className="my-4">
                                <div className="text-center">
                                    <Link to={"/register"} className="btn btn-lg btn-success">
                                        {translator('navigation_partners_register')}&nbsp;<i className="fa fa-arrow-right"/>
                                    </Link>
                                </div>
                            </div>

                            <h3>Next steps</h3>
                            <ol>
                                <li>Sign up as a partner</li>
                                <li>Set a service you provide </li>
                                <li>Choose the areas you would like to work in by a ZIP code</li>
                                <li>Take a request and wait for a confirmation</li>
                                <li>Once you have a sufficient number of requests you can start your own green business!</li>
                            </ol>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}

export default withRouter(RegisterIntroduction)
