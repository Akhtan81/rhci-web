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

                            <h2 className="text-center mb-4">What if you had the chance to be a part of something that is going to make people’s lives easier?</h2>
                            <h4 className="text-center mb-4">People who simply want to help the planet and the environment in their own little way?</h4>

                            <p>Here at Mobile Recycling Systems, we aim to make people’s lives easier by <b>automating their recycling processes</b>. We want to make it simpler for people to take care of their recyclables with the tap of a button on their smartphone, without even having to think about it. And we are looking for partners to help us realize our vision. People who are passionate about recycling, caring for their planet and their environment, and happy to help others who share that same enthusiasm to work towards a brighter, greener Earth.</p>

                            <p>Remember when Taxi service app completely took over the rideshare industry? With the release of their mobile app and a simple concept that allows anyone to use their car to make money helping others looking for rides, they upended the taxi industry and became a game-changer. Here at <b>Mobile Recycling Systems</b>, we are going to do the same thing. We are looking for excited, passionate people who want to help collect recyclables for others.</p>

                            <h3>How does it work?</h3>

                            <p>Simple! You, as a prospective driver partner with Mobile Recycling Systems, can sign up right here at
                                &nbsp;<a href="https://admin.mobilerecycling.net">admin.mobilerecycling.net</a>&nbsp;
                                as a partner. You’ll choose the area you want to collect for, and await confirmation for your request.</p>

                            <p>A client will register on our mobile app where they will enter their personal data such as address, phone number, and email, and also data for conducting an online transaction for cash rewards. Once the client collects a minimum amount of recyclables, they will send a request through the app for pickup of their recyclables. This request and all information pertaining to it will come straight to your personal account. Once you have accumulated a sufficient number of applications, you can go collect the recyclable materials from your clients!</p>

                            <p>The clients will have a choice of how they will like to be paid for the collected materials--either in good-old fashioned cash, or through bank transfer through our integrated payment system in the mobile app.</p>

                            <p className="font-weight-bold font-italic">You set your own price and minimum quantity of raw materials!</p>

                            <p>You can choose an area by ZIP code for collecting recyclables. The site administrator will approve your request, or offer you other options. Our collector partners pay an annual or monthly fee for use of the app, and the amount will be set after a free one month trial period.</p>

                            <h4 className="text-center">Sound interesting to you?</h4>

                            <p>Sign up with <b>Mobile Recycling Systems</b> today to be the first in your area! You can help make people’s lives easier by helping them reach their goals with recycling, and completely automating their recycling processes for them. Making not only happy clients, but a happier environment for all of us!</p>

                            <div className="my-4">
                                <h4 className="text-center">What are you waiting for? Sign up today!</h4>

                                <div className="text-center">
                                    <Link to={"/register"} className="btn btn-lg btn-success">
                                        {translator('navigation_partners_register')}&nbsp;<i className="fa fa-arrow-right"/>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }
}

export default withRouter(RegisterIntroduction)
