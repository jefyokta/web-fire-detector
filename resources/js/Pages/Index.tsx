import { useState } from "react";
import { ReactIcon } from "./../components/icon/react"
import Application from "../components/Application";

const Index = () => {
  const [counter, setCounter] = useState(0);

  return (
    <Application />
  );
};

export default Index;
