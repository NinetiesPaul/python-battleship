import random
import time
import os

def printBoard(board, clearScreen = False):
    if clearScreen:
        os.system('cls||clear')
        
    print(message)
    print("__|A B C D E F G H I J")
    for i,row in enumerate(board):
        print(str(i + 1).rjust(2, "0"), end="|")
        print(" ".join(row))

def buildBoard(showLabel = False):
    coordinates = []
    board = []
    
    for i in range(10):
        board.append(["~" for i in range(10)])
    
    for ship in ['eeeee', 'dddd', 'ccc', 'bb', 'a']:
        availableCoords = []
        orientation = random.choice(['vertical', 'horizontal'])
        
        if orientation == 'horizontal':
            for y, row in enumerate(board):
                currentSequence = []
                for x, column in enumerate(row):
                    if board[y][x] == "~":
                        currentSequence.append([x,y])
                    else:
                        if len(currentSequence) > 0:
                            currentSequence = []
                    if len(currentSequence) == len(ship) :
                        availableCoords.append(currentSequence)
                        currentSequence = []
        else:
            rearrengedBoard = []
    
            for x,column in enumerate(board[0]):
                rearrengedRow = []
                for i in range(10):
                    rearrengedRow.append(board[i][x])
                rearrengedBoard.append(rearrengedRow)
        
            for y, row in enumerate(rearrengedBoard):
                currentSequence = []
                for x, column in enumerate(row):
                    if rearrengedBoard[y][x] == "~":
                        currentSequence.append([y, x])
                    else:
                        if len(currentSequence) > 0:
                            currentSequence = []
                    if len(currentSequence) == len(ship) :
                        availableCoords.append(currentSequence)
                        currentSequence = []
    
        shipPlacement = random.choice(availableCoords)
    
        for coord in shipPlacement:
            board[coord[1]][coord[0]] = ship[0] if showLabel else "~"
            coordinates.append(",".join([ str(coord[0]), str(coord[1]) ]))

    return board, coordinates

playerBoard, allPlayerCoords = buildBoard(True)
computerBoard, allComputerCoords = buildBoard()

"""
App's main logic
"""
letterPos = 'ABCDEFGHIJ'
playerTurn = True
pickedEnemyCoords = []
pickedPlayerCoords = []
computerFireAgain = False
runProgram = True
winner = ""

while runProgram:
    os.system('cls||clear')

    currentTurn = "Player" if playerTurn else "Computer"
    currentBoard = "Computer" if playerTurn else "Player"
    message = currentTurn + "'s turn! | Showing " + currentBoard + "'s board"
    printBoard(computerBoard if playerTurn else playerBoard)

    if len(allPlayerCoords) == 0 or len(allComputerCoords) == 0:
        runProgram = False
        winner = "Game ended!" + (" Player won!" if len(allComputerCoords) == 0 else " Computer won!" )

    if not playerTurn:
        pickingCoord = True 

        while pickingCoord:

            if computerFireAgain == False:
                cpuX, cpuY = random.randint(0,9), random.randint(0,9)

            else:
                directionsToMove = ['up', 'down', 'left', 'right']

                if cpuY == 9:
                    directionsToMove.pop(directionsToMove.index('down'))
                if cpuY == 0:
                    directionsToMove.pop(directionsToMove.index('up'))
                if cpuX == 0:
                    directionsToMove.pop(directionsToMove.index('left'))
                if cpuX == 9:
                    directionsToMove.pop(directionsToMove.index('right'))   

                directionToMove = random.choice(directionsToMove)

                newCpuX = cpuX+1 if directionToMove == 'right' else cpuX-1 if directionToMove == 'left' else cpuX
                newCpuY = cpuY+1 if directionToMove == 'down' else cpuY-1 if directionToMove == 'up' else cpuY

                cpuX, cpuY = newCpuX, newCpuY

            if [cpuX,cpuY] not in pickedEnemyCoords:
                pickingCoord = False
                continue

        positionText = ",".join([letterPos[cpuX], str(cpuY + 1)])
        print("Firing again at", positionText +  "...") if computerFireAgain else print("Firing at", positionText + "...")
        time.sleep(3)

        outcome = 'Missed!'
        coord = ",".join([str(cpuX), str(cpuY)])
        if coord in allPlayerCoords:
            outcome = "That's a hit!"
            playerBoard[cpuY][cpuX] = 'X'
            allPlayerCoords.remove(coord)
            playerTurn = False
            computerFireAgain = True
        else:
            playerBoard[cpuY][cpuX] = 'O'
            playerTurn = True # False for Debug
            computerFireAgain = False

        if outcome != 'Missed!':
            printBoard(playerBoard, True)
        print(outcome)
        pickedEnemyCoords.append([cpuX, cpuY])
        input("Press any key to continue.") # Comment this for Debug

    else:
        option = input("Pick a coordinate (letter,number): ")

        if option == "quit":
            runProgram = False
            print("Bye")
        elif len(option) == 0 or "," not in option or len(option.split(',')) != 2:
            continue
        else:
            posX, posY = letterPos.find(option.split(',')[0].upper()), option.split(',')[1]
            
            if posX == -1 or posY.isnumeric() == False:
                print("Try again")
            elif posY.isnumeric() and int(posY) not in [1,2,3,4,5,6,7,8,9,10]:
                print("Try again")
            else:
                posY = int(posY)
                posY -= 1

                if [posX, posY] in pickedPlayerCoords:
                    continue

                print("Firing at", ",".join([letterPos[posX], str(posY + 1)]) + "...")
                time.sleep(3)

                outcome = 'Missed!'
                coord = ",".join([str(posX), str(posY)])
                if coord in allComputerCoords:
                    outcome = "That's a hit! Get ready to firing again!"
                    computerBoard[posY][posX] = 'X'
                    allComputerCoords.remove(coord)
                    playerTurn = True
                else:
                    computerBoard[posY][posX] = 'O'
                    playerTurn = False
                pickedPlayerCoords.append([posX, posY])

                if outcome != 'Missed!':
                    printBoard(computerBoard, True)
                print(outcome)
                input("Press any key to continue. ")

print(winner)