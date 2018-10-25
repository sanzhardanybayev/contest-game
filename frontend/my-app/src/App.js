import React, { Component } from 'react'
import axios from 'axios'
import logo from './logo.svg'
import './App.css'
import 'bootstrap/dist/css/bootstrap-grid.min.css'
import Button from '@material-ui/core/Button'

class App extends Component {

    state = {
        teams: [],
        agroup: [],
        bgroup: [],
    }

    getTeams = async () => {
        let self = this
        axios.get('http://localhost:8080/api/teams')
            .then(function (response) {
                // handle success
                self.setState({
                    teams: response.data.teams
                })
                console.log(response)
            })
            .catch(function (error) {
                // handle error
                console.log(error)
            })

    }
    getGroups = async () => {
        let self = this
        axios.get('http://localhost:8080/api/divide')
            .then(function (response) {

                if(response.data.status == 500){
                    alert('Grous have already been created!')
                } else {
                    // handle success
                     self.setState({
                        agroup: response.data.agroups
                    })
                    self.setState({
                        bgroup: response.data.bgroups
                    })
                }
                console.log(response)
            })
            .catch(function (error) {
                // handle error
                console.log(error)
            })

    }

    render() {
        return (
            <div className="App" >
                <header className="App-header" >
                    <h3 >Tournament Game</h3 >
                </header >

                <section >
                    <Button onClick={this.getTeams} variant="contained" color="primary" >
                        Generate Teams
                    </Button >

                    {this.state.teams.length > 0 ?    <Button onClick={this.getGroups} variant="contained" color="primary" >
                        Divide by groups
                    </Button > : null}
                </section >

                <div className={'container'} >
                    <div className={'row'} >
                        {this.state.teams.length > 0 ? <h3> Teams: </h3> : ''}
                        <ul className={''} >
                            {this.state.teams.map((team, i) => {
                                return <li >{team.name}</li >
                            })}
                        </ul >
                    </div >

                    <div className={'row'} >
                        {this.state.agroup.length > 0 ? <h3> A Group: </h3> : ''}
                        <ul className={''} >
                            {this.state.agroup.map((team, i) => {
                                return <li >{team.team.name} </li >
                            })}
                        </ul >


                        {this.state.bgroup.length > 0 ? <h3> B Group: </h3> : ''}
                        <ul className={''} >
                            {this.state.bgroup.map((team, i) => {
                                return <li >{team.team.name}</li >
                            })}
                        </ul >
                    </div >
                </div >

            </div >
        )
    }
}

export default App
